<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Spatial;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Spatial\GeoPointType;
use stdClass;

class GeoPointTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $geoPointType = new GeoPointType();
        $geoPointType->setName($name);

        if (isset($configuration->ignore_malformed)) {
            $geoPointType->setIgnoreMalformed((bool)$configuration->ignore_malformed);
        }
        if (isset($configuration->ignore_z_value)) {
            $geoPointType->setIgnoreZValue((bool)$configuration->ignore_z_value);
        }
        if (isset($configuration->index)) {
            $geoPointType->setIndex((bool)$configuration->index);
        }
        if (isset($configuration->doc_values)) {
            $geoPointType->setDocValues((bool)$configuration->doc_values);
        }
        if (isset($configuration->null_value)) {
            /** @var array<string, float>|string $nullValue */
            $nullValue = is_object($configuration->null_value)
                ? (array)$configuration->null_value
                : $configuration->null_value;
            $geoPointType->setNullValue($nullValue);
        }

        return $geoPointType;
    }
}
