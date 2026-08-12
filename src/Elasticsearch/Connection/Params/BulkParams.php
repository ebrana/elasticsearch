<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

final class BulkParams extends AbstractParams
{
    public function __construct(
        protected ?string $wait_for_active_shards = null,
        protected null|bool|string $refresh = null,
        protected ?string $routing = null,
        protected null|int|string $timeout = null,
        protected ?string $pipeline = null,
        protected ?bool $require_alias = null,
        protected ?bool $list_executed_pipelines = null,
        protected ?bool $pretty = null,
        protected ?bool $human = null,
        protected ?bool $error_trace = null,
        protected ?string $source = null,
        protected ?string $filter_path = null,
    ) {
    }

    protected function getParams(): array
    {
        return [
            'wait_for_active_shards',
            'refresh',
            'routing',
            'timeout',
            'pipeline',
            'require_alias',
            'list_executed_pipelines',
            'pretty',
            'human',
            'error_trace',
            'source',
            'filter_path',
        ];
    }
}
