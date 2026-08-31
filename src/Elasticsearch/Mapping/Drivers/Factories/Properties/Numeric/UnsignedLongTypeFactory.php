<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\UnsignedLongType;
use stdClass;

class UnsignedLongTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $unsignedLongType = new UnsignedLongType();
        $unsignedLongType->setName($name);
        self::applyConfiguration($unsignedLongType, $configuration);

        return $unsignedLongType;
    }
}
