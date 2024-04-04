<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class NestedQuery implements Query
{
    public function __construct(
        private string $path,
        private Query $query
    ) {
    }

    public function toArray(): Generator
    {
        yield 'nested' => [
            'path'  => $this->path,
            'query' => iterator_to_array($this->query->toArray()),
        ];
    }
}
