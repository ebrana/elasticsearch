<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\LengthFilter;
use stdClass;

class LengthFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{min?: int, max?: int} $configuration
     */
    public static function create(string $name, stdClass $configuration): LengthFilter
    {
        return new LengthFilter(
            $name,
            (int)($configuration->min ?? LengthFilter::DEFAULT_MIN),
            (int)($configuration->max ?? LengthFilter::DEFAULT_MAX)
        );
    }
}
