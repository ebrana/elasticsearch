<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers;

use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyResolverInterface;
use Elasticsearch\Mapping\Exceptions\DuplicityPropertyException;
use Elasticsearch\Mapping\Exceptions\IndexDefinitionNotFoundException;
use Elasticsearch\Mapping\Exceptions\MissingKeyResolverException;
use Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Analysis;
use Elasticsearch\Mapping\Settings\Analyzer;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\MappingInterface;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

class AnnotationDriver implements DriverInterface
{
    private int $level = 0;
    private ?KeyResolverInterface $keyResolver = null;

    public function setKeyResolver(?KeyResolverInterface $keyResolver): void
    {
        $this->keyResolver = $keyResolver;
    }

    /**
     * @param class-string $source
     * @throws DuplicityPropertyException
     * @throws IndexDefinitionNotFoundException
     * @throws MissingKeyResolverException
     * @throws MissingObjectTypeTemplateFiledsException
     * @throws ReflectionException
     */
    public function loadMetadata(string $source): Index
    {
        if (false === class_exists($source)) {
            throw new RuntimeException(sprintf('Class "%s" not exists or cannot loadable.', $source));
        }

        $reflection = new ReflectionClass($source);
        $indexMetadata = null;
        $index = $reflection->getAttributes(Index::class);

        if (empty($index)) {
            // hledej v dalsich levelech
            $parentClass = $reflection->getParentClass();
            if (false !== $parentClass) {
                $this->level++;
                $indexMetadata = $this->loadMetadata($parentClass->getName());
            }

            if (null === $indexMetadata) {
                throw new IndexDefinitionNotFoundException($source);
            }
        } else {
            /** @var Index $indexMetadata */
            $indexMetadata = $index[0]->newInstance();
        }

        // hledej filtry a analyzery
        $analyzers = $reflection->getAttributes(Analyzer::class);
        $analysis = null;

        if (!empty($analyzers)) {
            $analysis = new Analysis();
            foreach ($analyzers as $analyzer) {
                $analysis->addAnalyzer($analyzer->newInstance());
            }
        }

        $filters = $reflection->getAttributes(AbstractFilter::class, ReflectionAttribute::IS_INSTANCEOF);
        if (!empty($filters)) {
            if (null === $analysis) {
                $analysis = new Analysis();
            }
            foreach ($filters as $filter) {
                $filterInstance = $filter->newInstance();
                $analysis->addFilter($filterInstance);
            }
        }

        $tokenizers = $reflection->getAttributes(AbstractTokenizer::class, ReflectionAttribute::IS_INSTANCEOF);
        if (!empty($tokenizers)) {
            if (null === $analysis) {
                $analysis = new Analysis();
            }
            foreach ($tokenizers as $tokenizer) {
                $tokenizerInstance = $tokenizer->newInstance();
                $analysis->addTokenizer($tokenizerInstance);
            }
        }

        if ($analysis) {
            $indexMetadata->setAnalysis($analysis);
        }

        // resolvuj property
        $properties = $reflection->getProperties();
        if (0 === $this->level) {
            $this->resolveProperties($indexMetadata, $reflection, $properties);
            $indexMetadata->setEntityClass($source);
        } else {
            $this->level--;
        }

        return $indexMetadata;
    }

    /**
     * @param ReflectionProperty[] $properties
     * @throws \Elasticsearch\Mapping\Exceptions\DuplicityPropertyException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \ReflectionException
     */
    private function resolveProperties(
        Index $indexMetadata,
        ReflectionClass $reflection,
        array $properties
    ): void {
        if (null === $indexMetadata->getName()) {
            $indexMetadata->setName(strtolower($reflection->getShortName()));
        }

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(MappingInterface::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var AbstractType|MappingInterface $instance */
                $instance = $attribute->newInstance();

                if ($instance instanceof ObjectType) {
                    if ($instance->getMappedBy()) {
                        if ($instance->isKeyResolver()) {
                            throw new \LogicException(
                                sprintf(
                                    'Dont use keyResolver, when using mappedBy attribute. Field "%s" in class "%s".',
                                    $property->getName(),
                                    $reflection->getName())
                            );
                        }
                        $this->resolveObjectTypeByMapping($instance, $reflection);
                    } else if ($instance->isKeyResolver()) {
                        /** @var AbstractType|null $template */
                        $template = $instance->getProperties()->get(0);
                        if (null !== $template) {
                            $instance->setFieldsTemplate($template);
                            $this->resolveObjectTypePropertiesByKeyResolver($instance, $reflection, (string)$instance->getFieldName());
                            $instance->getProperties()->remove(0);
                        } else {
                            $this->resolveObjectTypePropertiesByKeyResolver($instance, $reflection, (string)$instance->getFieldName());
                        }
                    }
                }

                if (!$instance instanceof AbstractType) {
                    throw new RuntimeException(sprintf('Syntax error: %s.', $reflection->getName()));
                }

                $propertyName = $property->getName();
                if ('' === $instance->getName()) {
                    $instance->setName($propertyName);
                }
                $instance->setFieldName($propertyName);
                $indexMetadata->addProperty($instance);
            }
        }
    }

    /**
     * @throws MissingKeyResolverException
     * @throws MissingObjectTypeTemplateFiledsException
     */
    private function resolveObjectTypePropertiesByKeyResolver(
        ObjectType $classType,
        ReflectionClass $reflection,
        string $propertyName
    ): void {
        if (null === $this->keyResolver) {
            throw new MissingKeyResolverException();
        }
        // mam typ object a zaroven rikam, ze chci klice pres resolver
        $keys = $this->keyResolver->resolve();
        foreach ($keys as $key) {
            $template = $classType->getFieldsTemplate();
            if (null === $template) {
                throw new MissingObjectTypeTemplateFiledsException($reflection->getName(), $propertyName);
            }

            $field = clone $template;
            $field->setName($key);
            $classType->addProperty($field);
        }
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \ReflectionException
     */
    private function resolveObjectTypeByMapping(
        ObjectType $objectType,
        ReflectionClass $reflectionClass
    ): void {
        $referenceClass = $objectType->getMappedBy();
        if (null === $referenceClass || false === class_exists($referenceClass)) {
            throw new RuntimeException(sprintf('Class "%s" not exists or cannot loadable.', $referenceClass));
        }

        $reflection = new ReflectionClass($referenceClass);
        $referenceProperties = $reflection->getProperties();
        foreach ($referenceProperties as $referenceProperty) {
            $refAttributes = $referenceProperty->getAttributes(MappingInterface::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($refAttributes as $rValue) {
                /** @var AbstractType $referenceInstance */
                $referenceInstance = $rValue->newInstance();
                if ($referenceInstance->getContext() === $reflectionClass->getName()) {
                    $propertyName = $referenceProperty->getName();
                    if ('' === $referenceInstance->getName()) {
                        $referenceInstance->setName($propertyName);
                    }
                    $referenceInstance->setFieldName($propertyName);
                    if ($referenceInstance instanceof ObjectType) {
                        if ($referenceInstance->getMappedBy()) {
                            $this->resolveObjectTypeByMapping($referenceInstance, $reflection);
                        } else if ($referenceInstance->isKeyResolver()) {
                            $this->resolveObjectTypePropertiesByKeyResolver($referenceInstance, $reflection,
                                (string)$referenceInstance->getFieldName());
                        }
                    }
                    $objectType->addProperty($referenceInstance);
                }
            }
        }
    }
}
