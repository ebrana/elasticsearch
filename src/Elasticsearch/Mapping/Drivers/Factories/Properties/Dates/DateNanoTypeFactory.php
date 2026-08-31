<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Dates;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Dates\DateNanoType;
use stdClass;

class DateNanoTypeFactory implements PropertyFactoryInterface
{
    use DateConfigurationTrait;

    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $dateNanoType = new DateNanoType();
        $dateNanoType->setName($name);
        self::applyConfiguration($dateNanoType, $configuration);

        return $dateNanoType;
    }
}
