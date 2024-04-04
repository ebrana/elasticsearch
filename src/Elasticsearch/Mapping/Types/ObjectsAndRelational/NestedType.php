<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\ObjectsAndRelational;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class NestedType extends ObjectType
{
    private bool $include_in_parent;
    private bool $include_in_root;

    public function __construct(
        bool $keyResolver = false,
        Dynamic $dynamic = Dynamic::TRUE,
        bool $include_in_parent = false,
        bool $include_in_root = false,
        ?AbstractType $fieldsTemplate = null,
        array $properties =  [],
        ?string $name = null,
        ?string $context = null,
        protected ?string $mappedBy = null,
    ) {
        parent::__construct($dynamic, $keyResolver, $fieldsTemplate, $properties, $name, $context, $mappedBy);
        $this->type = 'nested';
        $this->include_in_parent = $include_in_parent;
        $this->include_in_root = $include_in_root;
    }

    public function isIncludeInParent(): bool
    {
        return $this->include_in_parent;
    }

    public function setIncludeInParent(bool $include_in_parent): void
    {
        $this->include_in_parent = $include_in_parent;
    }

    public function isIncludeInRoot(): bool
    {
        return $this->include_in_root;
    }

    public function setIncludeInRoot(bool $include_in_root): void
    {
        $this->include_in_root = $include_in_root;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $collection->set('type', $this->getType());
        $properties = [];

        /** @var AbstractType $property */
        foreach ($this->getProperties() as $property) {
            $properties[$property->getName()] = $property->getCollection()->toArray();
        }

        $collection->set('properties', $properties);

        if ($this->isIncludeInParent()) {
            $collection->set('include_in_parent', true);
        }

        if ($this->isIncludeInRoot()) {
            $collection->set('include_in_root', true);
        }

        return $collection;
    }
}
