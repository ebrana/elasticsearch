<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\PropertiesResolver;

use Elasticsearch\Mapping\Drivers\Factories\Properties\BooleanTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords\KeywordTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\FloatTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\IntegerTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Text\TextTypeFactory;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\NestedType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use stdClass;

final class PropertiesResolver
{
    /** @var class-string<PropertyFactoryInterface>[] */
    private array $propertiesFactories = [
        'text'    => TextTypeFactory::class,
        'keyword' => KeywordTypeFactory::class,
        'integer' => IntegerTypeFactory::class,
        'float'   => FloatTypeFactory::class,
        'boolean' => BooleanTypeFactory::class,
    ];

    /**
     * @throws \Elasticsearch\Mapping\Exceptions\DuplicityPropertyException
     */
    public function resolveProperties(stdClass $mappings, Index $index, ?ObjectType $objectType = null): void
    {
        /** @var stdClass $property */
        foreach ($mappings->properties as $key => $property) {
            $field = null;
            if (isset($property->properties)) {
                if (isset($property->type)) {
                    $field = match ($property->type) {
                        'nested' => new NestedType(name: $key),
                        default => new ObjectType(name: $key),
                    };
                } else {
                    $field = new ObjectType(name: $key);
                }
                $this->resolveProperties($property, $index, $field);
                $index->addProperty($field);
                continue; // property->properties ... next level
            }
            if (isset($this->propertiesFactories[$property->type])) {
                $factory = $this->propertiesFactories[$property->type];
                $field = $factory::create($key, $property);

                if ($objectType) {
                    $objectType->addProperty($field);
                } else {
                    $index->addProperty($field);
                }
            }
        }
    }
}
