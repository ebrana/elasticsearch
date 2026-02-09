<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

final class IndexDocumentParams extends AbstractParams
{
    public function __construct(
        protected ?string $wait_for_active_shards = null,
        protected null|bool|string $refresh = null,
        protected ?string $routing = null,
        protected ?string $timeout = null,
        protected ?int $version = null,
        protected ?int $if_seq_no = null,
        protected ?int $if_primary_term = null,
        protected ?string $pipeline = null,
        protected ?bool $require_alias = null,
        protected ?bool $pretty = null,
        protected ?bool $human = null,
        protected ?bool $error_trace = null,
        protected ?string $source = null,
        protected ?VersionType $version_type = null,
        protected ?OpType $op_type = null,
    ) {
    }

    protected function getParams(): Generator
    {
        yield from [
            'wait_for_active_shards',
            'op_type',
            'refresh',
            'routing',
            'timeout',
            'version',
            'version_type',
            'if_seq_no',
            'if_primary_term',
            'pipeline',
            'require_alias',
            'pretty',
            'human',
            'error_trace',
            'source'
        ];
    }
}
