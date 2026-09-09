<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\ShortType;
use stdClass;

class ShortTypeFactory implements PropertyFactoryInterface
{
    use NumericConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $shortType = new ShortType();
        $shortType->setName($name);
        self::applyConfiguration($shortType, $configuration);

        return $shortType;
    }
}
