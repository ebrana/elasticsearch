<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Properties\Text;

use Elasticsearch\Mapping\Drivers\Factories\Properties\PropertyFactoryInterface;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Text\MatchOnlyTextType;
use stdClass;

class MatchOnlyTextTypeFactory implements PropertyFactoryInterface
{
    /**
     * @param stdClass&object{copy_to?: string} $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractType
    {
        return new MatchOnlyTextType(
            isset($configuration->copy_to) ? (string)$configuration->copy_to : null,
            $name
        );
    }
}
