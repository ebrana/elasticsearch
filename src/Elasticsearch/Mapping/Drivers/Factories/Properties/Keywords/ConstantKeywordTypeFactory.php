<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Keywords;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Common\Keywords\ConstantKeywordType;
use stdClass;

class ConstantKeywordTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{value?: string} $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        return new ConstantKeywordType(
            isset($configuration->value) ? (string)$configuration->value : null,
            $name
        );
    }
}
