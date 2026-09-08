<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\TrimFilter;
use stdClass;

class TrimFilterFactory implements FilterFactoryInterface
{
    public static function create(string $name, stdClass $configuration): TrimFilter
    {
        return new TrimFilter($name);
    }
}
