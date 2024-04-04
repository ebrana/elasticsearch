<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Resolvers;

use Elasticsearch\Indexing\Exceptions\DataResolverExistsException;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use InvalidArgumentException;
use RuntimeException;

trait CollectionByMappingResolverTrait
{
    /**
     * @param ObjectType                 $property
     * @param IndexableEntityInterface[] $collection
     * @return array<int|string, array<string, mixed>|bool|float|int|string>
     * @throws \Elasticsearch\Indexing\Exceptions\DataResolverExistsException
     */
    private function resolveCollection(ObjectType $property, iterable $collection): array
    {
        $records = [];
        foreach ($collection as $relEntity) {
            $record = [];
            /** @var \Elasticsearch\Mapping\Types\AbstractType $relProperty */
            foreach ($property->getProperties() as $relProperty) {
                $objectPropertyName = $relProperty->getFieldName();
                if (null === $objectPropertyName) {
                    throw new RuntimeException('Property field name empty.');
                }
                $relGetter = $this->getGetter($relEntity, $relProperty);
                if (!method_exists($relEntity, $relGetter)) {
                    throw new DataResolverExistsException(sprintf('Entity "%s" does not have method "%s(...)".', get_class($relEntity), $relGetter));
                }
                $relValue = $relEntity->$relGetter();
                if ($relProperty instanceof ObjectType) {
                    if (!is_iterable($relValue)) {
                        throw new InvalidArgumentException('Resolving data for instance of ObjectType must be iterable.');
                    }
                    $record[$objectPropertyName] = $this->resolveCollection($relProperty, $relValue);
                } else {
                    if (!is_scalar($relValue) && null !== $relValue) {
                        throw new RuntimeException(
                            sprintf('Resolved value must be null or scalar. Entity "%s" and property "%s". Write your custom resolver instead.',
                                get_class($relEntity),
                                $relProperty->getFieldName()
                            )
                        );
                    }
                    $record[$objectPropertyName] = $relValue;
                }
            }

            $records[] = $record;
        }

        return $records;
    }

    private function getGetter(object $entity, AbstractType $type): string
    {
        $prefix = 'get';
        $propertyName = $type->getFieldName();
        if (null === $propertyName) {
            throw new RuntimeException('Property field name empty.');
        }
        $name = ucfirst($propertyName);
        if ($type instanceof BooleanType && method_exists($entity, 'is' . $name)) {
            $prefix = 'is';
        }

        return $prefix . $name;
    }
}
