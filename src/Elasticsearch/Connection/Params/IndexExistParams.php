<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

final class IndexExistParams extends AbstractParams
{
    public function __construct(
        protected ?bool $local = null,
        protected ?bool $ignore_unavailable = null,
        protected ?bool $allow_no_indices = null,
        protected ?bool $flat_settings = null,
        protected ?bool $include_defaults = null,
        protected ?bool $pretty = null,
        protected ?bool $human = null,
        protected ?bool $error_trace = null,
        protected ?string $source = null,
        protected ?string $filter_path = null,
        protected ?ExpandWildcards $expand_wildcards = null,
    ) {
    }

    protected function getParams(): Generator
    {
        yield from [
            'local',
            'ignore_unavailable',
            'allow_no_indices',
            'expand_wildcards',
            'flat_settings',
            'include_defaults',
            'pretty',
            'human',
            'error_trace',
            'source',
            'filter_path'
        ];
    }
}
