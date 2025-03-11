<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

final class CreateIndexParams extends AbstractParams
{
    public function __construct(
        protected ?string $wait_for_active_shards = null,
        protected ?string $timeout = null,
        protected ?string $master_timeout = null,
        protected ?bool $pretty = null,
        protected ?bool $human = null,
        protected ?bool $error_trace = null,
        protected ?string $source = null,
        protected ?string $filter_path = null,
    ) {
    }

    protected function getParams(): Generator
    {
        yield from [
            'wait_for_active_shards',
            'timeout',
            'master_timeout',
            'pretty',
            'human',
            'error_trace',
            'source',
            'filter_path'
        ];
    }
}
