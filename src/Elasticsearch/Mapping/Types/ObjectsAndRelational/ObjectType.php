<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\ObjectsAndRelational;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\ValidatorInterface;
use RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class ObjectType extends AbstractType implements ValidatorInterface
{
    protected Dynamic $dynamic;
    protected ArrayCollection $properties;

    /**
     * @param array<AbstractType> $properties
     */
    public function __construct(
        Dynamic $dynamic = Dynamic::TRUE,
        protected ?string $keyResolver = null,
        protected ?AbstractType $fieldsTemplate = null,
        array $properties =  [],
        ?string $name = null,
        ?string $context = null,
        protected ?string $mappedBy = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'object';
        $this->dynamic = $dynamic;
        $this->properties = new ArrayCollection($properties);

        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function __clone(): void
    {
        /** @var AbstractType $property */
        foreach ($this->properties as $key => $property) {
            $this->properties[$key] = clone $property;
        }
    }

    public function addProperty(AbstractType $type): void
    {
        $this->properties->set($type->getName(), $type);
    }

    public function hasKeyResolver(): bool
    {
        return $this->keyResolver !== null;
    }

    public function getKeyResolver(): ?string
    {
        return $this->keyResolver;
    }

    public function setFieldsTemplate(?AbstractType $fieldsTemplate): void
    {
        $this->fieldsTemplate = $fieldsTemplate;
    }

    public function getFieldsTemplate(): ?AbstractType
    {
        return $this->fieldsTemplate;
    }

    public function getProperties(): ArrayCollection
    {
        return $this->properties;
    }

    public function getDynamic(): Dynamic
    {
        return $this->dynamic;
    }

    public function setDynamic(Dynamic $dynamic): void
    {
        $this->dynamic = $dynamic;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $collection->remove('type');
        $properties = [];

        /** @var AbstractType $property */
        foreach ($this->getProperties() as $property) {
            $properties[$property->getName()] = $property->getCollection()->toArray();
        }

        $collection->set('properties', $properties);

        if ($this->getDynamic() !== Dynamic::TRUE) {
            $collection->set('dynamic', $this->getDynamic());
        }

        return $collection;
    }

    public function getMappedBy(): ?string
    {
        return $this->mappedBy;
    }

    public function validate(): void
    {
        if (null === $this->keyResolver && $this->properties->isEmpty()) {
            throw new RuntimeException(sprintf('Field "%s" has empty fields properties.', $this->getFieldName()));
        }

        if (null !== $this->keyResolver && null === $this->fieldsTemplate) {
            throw new RuntimeException(
                sprintf('Field "%s" has empty fieldsTemplate. Please set fieldsTemplate.', $this->getFieldName())
            );
        }

        if (null === $this->keyResolver && null !== $this->fieldsTemplate) {
            trigger_error('$fieldsTemplate is set. You didn\'t forget to set it $keyResolver property.', E_USER_WARNING);
        }
    }
}
