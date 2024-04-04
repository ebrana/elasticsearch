<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

trait MultiFieldsTrait
{
    protected ArrayCollection $fields;

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function addField(AbstractType $field): void
    {
        $this->fields->set($field->getName(), $field);
    }

    /**
     * @param AbstractType[]|null $fields
     */
    protected function createFields(?array $fields): void
    {
        $this->fields = new ArrayCollection();
        if ($fields) {
            foreach ($fields as $field) {
                if ($field instanceof AbstractType) {
                    $this->fields->set($field->getName(), $field);
                }
            }
        }
    }

    protected function extendCollectionByFields(ArrayCollection $collection): void
    {
        $properties = [];

        /** @var AbstractType $field */
        foreach ($this->getFields() as $field) {
            $properties[$field->getName()] = $field->getCollection()->toArray();
        }

        if (!empty($properties)) {
            $collection->set('fields', $properties);
        }
    }
}
