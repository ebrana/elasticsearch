<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Dates;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Dates\DateType;
use stdClass;

class DateTypeFactory implements PropertyFactoryInterface
{
    use DateConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $dateType = new DateType();
        $dateType->setName($name);
        self::applyConfiguration($dateType, $configuration);

        return $dateType;
    }
}
