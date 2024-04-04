<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Resolvers;

use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use RuntimeException;

trait ScalarValueResolverTrait
{
    private function resolveScalarByMetadata(
        DocumentInterface $document,
        IndexableEntityInterface $entity,
        Index $index
    ): void {
        foreach ($index->getProperties() as $property) {
            if ($property instanceof ObjectType) {
                continue;
            }
            $propertyName = $property->getFieldName();
            if (null === $propertyName) {
                throw new RuntimeException('Property field name empty.');
            }
            $name = ucfirst($propertyName);
            $getter = 'get' . $name;
            $booleanGetter = 'is' . $name;
            $isGetterExists = method_exists($entity, $getter);
            $isBoolGetterExists = method_exists($entity, $booleanGetter);

            if ($isGetterExists || $isBoolGetterExists) {
                $value = $isGetterExists ? $entity->$getter() : $entity->$booleanGetter();

                if (null === $value || is_scalar($value)) {
                    $document->set($propertyName, $value);
                }
            }
        }
    }
}
