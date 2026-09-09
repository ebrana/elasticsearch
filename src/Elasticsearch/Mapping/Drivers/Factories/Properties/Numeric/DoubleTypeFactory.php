<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\DoubleType;
use stdClass;

class DoubleTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $doubleType = new DoubleType();
        $doubleType->setName($name);
        self::applyConfiguration($doubleType, $configuration);

        return $doubleType;
    }
}
