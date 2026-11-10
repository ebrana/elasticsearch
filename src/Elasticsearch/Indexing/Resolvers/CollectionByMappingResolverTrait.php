<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Resolvers;

use Elasticsearch\Indexing\Exceptions\DataResolverExistsException;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\SourceOnly;
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
                // keyed by the ES field name (`name:`), which may differ from the PHP property
                $objectPropertyName = $relProperty->getName();
                if ($relProperty instanceof ObjectType) {
                    if (!is_iterable($relValue)) {
                        throw new InvalidArgumentException('Resolving data for instance of ObjectType must be iterable.');
                    }
                    /** @var IndexableEntityInterface[] $typedValue */
                    $typedValue = $relValue;
                    $record[$objectPropertyName] = $this->resolveCollection($relProperty, $typedValue);
                    continue;
                }

                $record[$objectPropertyName] = $this->resolveScalarOrSourceValue($relEntity, $relProperty, $relValue);
            }

            $records[] = $record;
        }

        return $records;
    }

    /**
     * A SourceOnly field goes to `_source` as it is - null, scalar or array (what Document::set()
     * accepts); any other field must resolve to null or a scalar.
     *
     * @return scalar|array<string|int, mixed>|null
     * @throws RuntimeException
     */
    private function resolveScalarOrSourceValue(object $entity, AbstractType $property, mixed $value): mixed
    {
        if (null === $value || is_scalar($value)) {
            return $value;
        }
        if ($property instanceof SourceOnly && is_array($value)) {
            return $value;
        }

        throw new RuntimeException(sprintf(
            '%s Entity "%s" and property "%s". Write your custom resolver instead.',
            $property instanceof SourceOnly
                ? 'Resolved value of SourceOnly field must be null, scalar or array.'
                : 'Resolved value must be null or scalar.',
            get_class($entity),
            $property->getFieldName()
        ));
    }

    private function getGetter(object $entity, AbstractType $type): string
    {
        $prefix = 'get';
        $propertyName = $type->getFieldName();
        if (null === $propertyName) {
            throw new RuntimeException('Property field name empty.');
        }
        $name = ucfirst($propertyName);
        // booleans (and source-only fields, whose type is unknown) may use an is*() getter
        if (($type instanceof BooleanType || $type instanceof SourceOnly) && method_exists($entity, 'is' . $name)) {
            $prefix = 'is';
        }

        return $prefix . $name;
    }
}
