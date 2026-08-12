<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Settings\Filters\AbstractSynonymFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\SynonymFormat;
use Elasticsearch\Mapping\Settings\Filters\SynonymFilter;
use Elasticsearch\Mapping\Settings\Filters\SynonymGraphFilter;
use stdClass;

class SynonymFilterFactory implements FilterFactoryInterface
{
    /**
     * @param stdClass&object{
     *     type?: string,
     *     synonyms?: string[],
     *     synonyms_path?: string,
     *     synonyms_set?: string,
     *     expand?: bool,
     *     lenient?: bool,
     *     format?: string,
     *     updateable?: bool
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): AbstractSynonymFilter
    {
        $arguments = [
            $name,
            $configuration->synonyms ?? null,
            $configuration->synonyms_path ?? null,
            $configuration->synonyms_set ?? null,
            (bool)($configuration->expand ?? true),
            (bool)($configuration->lenient ?? false),
            isset($configuration->format) ? SynonymFormat::tryFrom($configuration->format) : null,
            (bool)($configuration->updateable ?? false),
        ];

        return 'synonym_graph' === ($configuration->type ?? null)
            ? new SynonymGraphFilter(...$arguments)
            : new SynonymFilter(...$arguments);
    }
}
