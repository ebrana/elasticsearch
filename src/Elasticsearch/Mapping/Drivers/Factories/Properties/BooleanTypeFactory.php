<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties;

use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use stdClass;

class BooleanTypeFactory implements PropertyFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $booleanType = new BooleanType();
        $booleanType->setName($name);

        return $booleanType;
    }
}
