<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Resolvers;

use Doctrine\Common\Collections\Collection;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\AbstractType;
use RuntimeException;

trait CollectionByKeyResolverTrait
{
    private function resolveCollectionsByMetadata(
        DocumentInterface $document,
        IndexableEntityInterface $entity,
        Index $index,
        callable $keyResolver,
    ): void {
        foreach ($index->getProperties() as $property) {
            $this->resolveCollectionsByField($document, $entity, $property, $keyResolver);
        }
    }

    private function resolveCollectionsByField(
        DocumentInterface $document,
        IndexableEntityInterface $entity,
        AbstractType $field,
        callable $keyResolver,
        ?callable $valueResolver = null
    ): void {
        $collection = $this->getCollection($field, $entity);
        if ($collection) {
            $record = [];
            $propertyTypeName = $field->getName();
            $propertyTypeNameGetter = 'get' . ucfirst($propertyTypeName);
            foreach ($collection as $item) {
                if (false === is_object($item)) {
                    throw new RuntimeException(sprintf('Collection items in class %s and property %s is not objects. Write your custom resolver instead.',
                        get_class($entity), $field->getFieldName()));
                }
                if (!method_exists($item, $propertyTypeNameGetter)) {
                    throw new RuntimeException(
                        sprintf('Entity "%s" does not have method "%s". Write your custom resolver instead.', get_class($item), $propertyTypeNameGetter)
                    );
                }
                $value = null !== $valueResolver ? $valueResolver($item) : $item->$propertyTypeNameGetter();
                if (!is_scalar($value) && null !== $value) {
                    throw new RuntimeException(
                        sprintf('Resolved value must be null or scalar. Entity "%s" and property "%s". Write your custom resolver instead.',
                            get_class($item),
                            $field->getFieldName()
                        )
                    );
                }
                $key = $keyResolver($item);
                if (false === is_string($key)) {
                    throw new RuntimeException('KeyResolver must return string.');
                }
                $record[$key] = $value;
            }
            $document->set($propertyTypeName, $record);
        }
    }

    private function getCollection(AbstractType $field, IndexableEntityInterface $entity): ?Collection
    {
        $propertyName = $field->getFieldName();
        if (null === $propertyName) {
            throw new RuntimeException('Property field name empty.');
        }
        $name = ucfirst($propertyName);
        $getter = 'get' . $name;
        $isGetterExists = method_exists($entity, $getter);

        if (false === $isGetterExists) {
            throw new RuntimeException(sprintf('Entity "%s" does not have method "%s". Write your custom resolver instead.', get_class($entity), $getter));
        }

        $collection = $entity->$getter();

        return $collection instanceof Collection ? $collection : null;
    }
}
