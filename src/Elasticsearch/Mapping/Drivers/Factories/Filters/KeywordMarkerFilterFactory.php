<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\KeywordMarkerFilter;
use stdClass;

class KeywordMarkerFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{
     *     keywords?: string[],
     *     keywords_path?: string,
     *     keywords_pattern?: string,
     *     ignore_case?: bool
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): KeywordMarkerFilter
    {
        return new KeywordMarkerFilter(
            $name,
            $configuration->keywords ?? null,
            $configuration->keywords_path ?? null,
            $configuration->keywords_pattern ?? null,
            (bool)($configuration->ignore_case ?? false)
        );
    }
}
