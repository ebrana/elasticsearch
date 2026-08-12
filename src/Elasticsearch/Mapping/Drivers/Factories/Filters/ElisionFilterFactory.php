<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\ElisionFilter;
use stdClass;

class ElisionFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{articles?: string[], articles_path?: string, articles_case?: bool} $configuration
     */
    public static function create(string $name, stdClass $configuration): ElisionFilter
    {
        return new ElisionFilter(
            $name,
            $configuration->articles ?? null,
            $configuration->articles_path ?? null,
            (bool)($configuration->articles_case ?? false)
        );
    }
}
