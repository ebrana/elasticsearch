<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\Filters\PatternReplaceFilter;
use stdClass;

class PatternReplaceFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{pattern?: string, replacement?: string, all?: bool} $configuration
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): PatternReplaceFilter
    {
        if (!isset($configuration->pattern)) {
            throw new AttributeMissingException('Pattern Replace filter must define pattern.');
        }

        return new PatternReplaceFilter(
            $name,
            $configuration->pattern,
            $configuration->replacement ?? '',
            (bool)($configuration->all ?? true)
        );
    }
}
