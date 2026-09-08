<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\ScaledFloatType;
use stdClass;

class ScaledFloatTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    /**
     * Reads the required scaling_factor and the parameters shared by the numeric types.
     *
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        if (!isset($configuration->scaling_factor)) {
            throw new AttributeMissingException('Scaled float property must define scaling_factor.');
        }

        $scaledFloatType = new ScaledFloatType((float)$configuration->scaling_factor);
        $scaledFloatType->setName($name);
        self::applyConfiguration($scaledFloatType, $configuration);

        return $scaledFloatType;
    }
}
