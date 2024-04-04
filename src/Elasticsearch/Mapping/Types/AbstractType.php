<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractType implements MappingInterface
{
    protected string $type;
    protected ?string $context = null;
    private string $name = '';
    private ?string $fieldName = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = new ArrayCollection();
        $collection->set('type', $this->getType());

        return $collection;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function setFieldName(?string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }
}
