<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\UniqueFilter;
use stdClass;

class UniqueFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{only_on_same_position?: bool} $configuration
     */
    public static function create(string $name, stdClass $configuration): UniqueFilter
    {
        return new UniqueFilter($name, (bool)($configuration->only_on_same_position ?? false));
    }
}
