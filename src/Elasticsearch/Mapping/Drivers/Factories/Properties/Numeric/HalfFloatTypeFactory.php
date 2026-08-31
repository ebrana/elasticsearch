<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\HalfFloatType;
use stdClass;

class HalfFloatTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $halfFloatType = new HalfFloatType();
        $halfFloatType->setName($name);
        self::applyConfiguration($halfFloatType, $configuration);

        return $halfFloatType;
    }
}
