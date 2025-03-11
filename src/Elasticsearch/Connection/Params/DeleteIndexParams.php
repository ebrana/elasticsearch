<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

final class DeleteIndexParams extends AbstractParams
{
    public function __construct(
        protected ?string $timeout = null,
        protected ?string $master_timeout = null,
        protected ?bool $ignore_unavailable = null,
        protected ?bool $allow_no_indices = null,
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
            'timeout',
            'master_timeout',
            'ignore_unavailable',
            'allow_no_indices',
            'expand_wildcards',
            'pretty',
            'human',
            'error_trace',
            'source',
            'filter_path'
        ];
    }
}
