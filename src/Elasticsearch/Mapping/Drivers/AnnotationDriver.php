<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers;

use Closure;
use Elasticsearch\Mapping\Drivers\Resolvers\KeyResolver\KeyResolverInterface;
use Elasticsearch\Mapping\Exceptions\DuplicityPropertyException;
use Elasticsearch\Mapping\Exceptions\IndexDefinitionNotFoundException;
use Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException;
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
    private const string DEFAULT = 'default';
    private int $level = 0;

    /**
     * @param array<KeyResolverInterface|null>|null $keyResolvers
     */
    public function __construct(private ?array $keyResolvers = null)
    {
    }

    public function setDefaultKeyResolver(?KeyResolverInterface $keyResolver): void
    {
        if (null === $this->keyResolvers) {
            $this->keyResolvers = [];
        }
        $this->keyResolvers[self::DEFAULT] = $keyResolver;
    }

    /**
     * @param class-string $source
     * @throws DuplicityPropertyException
     * @throws IndexDefinitionNotFoundException
     * @throws MissingDefaultKeyResolverException
     * @throws MissingObjectTypeTemplateFiledsException
     * @throws ReflectionException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
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
     * @throws \Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \ReflectionException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
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
                    $this->resolveObjectTypeProperties($instance, $property->getName(), $reflection);
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
     * @throws MissingDefaultKeyResolverException
     * @throws MissingObjectTypeTemplateFiledsException
     * @throws MissingKeyResolverException
     * @throws ReflectionException
     */
    private function resolveObjectTypePropertiesByKeyResolver(
        ObjectType $objectType,
        ReflectionClass $reflection,
        string $propertyName
    ): void {
        // mam typ object a zaroven rikam, ze chci klice pres resolver
        $keyResolver = $this->getKeyResolver($objectType);
        $keys = $keyResolver->resolve();
        foreach ($keys as $key) {
            $template = $objectType->getFieldsTemplate();
            if (null === $template) {
                throw new MissingObjectTypeTemplateFiledsException($reflection->getName(), $propertyName);
            }

            $field = clone $template;
            $field->setName($key);

            if ($field instanceof ObjectType) {
                if ($field->isKeyResolver() || $field->getMappedBy() !== null) {
                    $name = $field->getFieldName() ?? $objectType->getName();
                    $this->resolveObjectTypeProperties($field, $name, $reflection);
                }
            }
            $objectType->addProperty($field);
        }
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \ReflectionException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     */
    private function resolveObjectTypeByMapping(
        ObjectType $objectType,
        ReflectionClass $reflectionClass,
    ): void {
        $this->resolvingByMappingBy(
            $objectType,
            $reflectionClass,
            function (AbstractType $referenceInstance) use ($objectType): void {
                $objectType->addProperty($referenceInstance);
            });
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \ReflectionException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     */
    private function resolvingByMappingBy(ObjectType $type, ReflectionClass $reflectionClass, Closure $callback): void
    {
        $referenceClass = $type->getMappedBy();
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
                            $this->resolveObjectTypePropertiesByKeyResolver(
                                $referenceInstance,
                                $reflection,
                                (string)$referenceInstance->getFieldName()
                            );
                        }
                    }
                    $callback($referenceInstance);
                }
            }
        }
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException
     */
    private function getKeyResolver(ObjectType $classType): KeyResolverInterface
    {
        if (is_bool($classType->getKeyResolver())) {
            if (!isset($this->keyResolvers[self::DEFAULT])) {
                throw new MissingDefaultKeyResolverException();
            }
            return $this->keyResolvers[self::DEFAULT];
        }

        $key = $classType->getKeyResolver();
        if (!isset($this->keyResolvers[$key])) {
            throw new MissingKeyResolverException($key);
        }
        return $this->keyResolvers[$key];
    }

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\MissingObjectTypeTemplateFiledsException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingDefaultKeyResolverException
     * @throws \ReflectionException
     * @throws \Elasticsearch\Mapping\Exceptions\MissingKeyResolverException
     */
    private function resolveObjectTypeProperties(
        ObjectType $instance,
        string $propertyName,
        ReflectionClass $reflection
    ): void {
        if ($instance->getMappedBy()) {
            if ($instance->isKeyResolver()) {
                throw new \LogicException(
                    sprintf(
                        'Dont use keyResolver, when using mappedBy attribute. Field "%s" in class "%s".',
                        $propertyName,
                        $reflection->getName())
                );
            }
            $this->resolveObjectTypeByMapping($instance, $reflection);
        } else {
            if ($instance->isKeyResolver()) {
                /** @var AbstractType|null $template */
                $template = $instance->getProperties()->get(0);
                $fieldName = (string)$instance->getFieldName();
                if (null !== $template) {
                    $instance->setFieldsTemplate($template);
                    $this->resolveObjectTypePropertiesByKeyResolver(
                        $instance,
                        $reflection,
                        $fieldName
                    );
                    $instance->getProperties()->remove(0);
                } else {
                    $this->resolveObjectTypePropertiesByKeyResolver(
                        $instance,
                        $reflection,
                        $fieldName
                    );
                }
            }
        }
    }
}
