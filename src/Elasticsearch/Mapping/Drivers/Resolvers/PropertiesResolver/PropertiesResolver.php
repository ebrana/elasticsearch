<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\PropertiesResolver;

use Elasticsearch\Mapping\Drivers\Factories\Properties\AliasTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\BinaryTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\BooleanTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Dates\DateNanoTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Dates\DateTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords\ConstantKeywordTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords\IcuCollationKeywordTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords\KeywordTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords\WildcardTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\ByteTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\DoubleTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\FloatTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\HalfFloatTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\IntegerTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\LongTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\ScaledFloatTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\ShortTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\UnsignedLongTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Spatial\GeoPointTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Specialized\RankFeatureTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Text\CompletionTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Text\MatchOnlyTextTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Text\TextTypeFactory;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\Helpers\MultiFieldsInterface;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\NestedType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use stdClass;

final class PropertiesResolver
{
    /** @var class-string<PropertyFactoryInterface>[] */
    private array $propertiesFactories = [
        'text'             => TextTypeFactory::class,
        'match_only_text'  => MatchOnlyTextTypeFactory::class,
        'keyword'          => KeywordTypeFactory::class,
        'constant_keyword' => ConstantKeywordTypeFactory::class,
        'wildcard'         => WildcardTypeFactory::class,
        'integer'          => IntegerTypeFactory::class,
        'long'             => LongTypeFactory::class,
        'short'            => ShortTypeFactory::class,
        'byte'             => ByteTypeFactory::class,
        'unsigned_long'    => UnsignedLongTypeFactory::class,
        'float'            => FloatTypeFactory::class,
        'double'           => DoubleTypeFactory::class,
        'half_float'       => HalfFloatTypeFactory::class,
        'scaled_float'     => ScaledFloatTypeFactory::class,
        'boolean'          => BooleanTypeFactory::class,
        'date'             => DateTypeFactory::class,
        'date_nanos'       => DateNanoTypeFactory::class,
        'binary'           => BinaryTypeFactory::class,
        'alias'            => AliasTypeFactory::class,
        'completion'       => CompletionTypeFactory::class,
        'rank_feature'     => RankFeatureTypeFactory::class,
        'geo_point'        => GeoPointTypeFactory::class,
        'icu_collation_keyword' => IcuCollationKeywordTypeFactory::class,
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
                if ($objectType) {
                    $objectType->addProperty($field);
                } else {
                    $index->addProperty($field);
                }
                continue; // property->properties ... next level
            }
            if (isset($this->propertiesFactories[$property->type])) {
                $factory = $this->propertiesFactories[$property->type];
                $field = $factory::create($key, $property);

                if ($field instanceof MultiFieldsInterface && isset($property->fields)) {
                    $this->resolveMultiFields($property->fields, $field);
                }

                if ($objectType) {
                    $objectType->addProperty($field);
                } else {
                    $index->addProperty($field);
                }
            }
        }
    }

    /**
     * Multi-fields (`fields`) cannot be nested into further multi-fields, so only one level is resolved.
     */
    private function resolveMultiFields(stdClass $fields, MultiFieldsInterface $parent): void
    {
        /** @var stdClass $configuration */
        foreach ((array)$fields as $name => $configuration) {
            if (!isset($configuration->type) || !isset($this->propertiesFactories[$configuration->type])) {
                continue;
            }

            $factory = $this->propertiesFactories[$configuration->type];
            $parent->addField($factory::create((string)$name, $configuration));
        }
    }
}
