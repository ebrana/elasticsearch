<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\ByteType;
use stdClass;

class ByteTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $byteType = new ByteType();
        $byteType->setName($name);
        self::applyConfiguration($byteType, $configuration);

        return $byteType;
    }
}
