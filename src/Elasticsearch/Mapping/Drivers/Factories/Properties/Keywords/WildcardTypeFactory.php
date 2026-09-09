<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Keywords\WildcardType;
use stdClass;

class WildcardTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{ignore_above?: int, null_value?: string} $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        $wildcardType = new WildcardType(
            (int)($configuration->ignore_above ?? 2147483647),
            isset($configuration->null_value) ? (string)$configuration->null_value : null
        );
        $wildcardType->setName($name);

        return $wildcardType;
    }
}
