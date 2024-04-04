<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use stdClass;

class IntegerTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $integerType = new IntegerType();
        $integerType->setName($name);

        return $integerType;
    }
}
