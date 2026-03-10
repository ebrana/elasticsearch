<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

readonly class NestedQuery implements Query
{
    public function __construct(
        private string $path,
        private Query $query
    ) {
    }

    public function toArray(): array
    {
        return ['nested' => [
            'path'  => $this->path,
            'query' => $this->query->toArray(),
        ]];
    }
}
