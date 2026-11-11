<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Resolvers;

use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\SourceOnly;
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

                // a source-only field may also carry an array, it is not mapped anyway
                if (null === $value || is_scalar($value) || ($property instanceof SourceOnly && is_array($value))) {
                    // keyed by the ES field name (`name:`), which may differ from the PHP property
                    $document->set($property->getName(), $value);
                }
            }
        }
    }
}
