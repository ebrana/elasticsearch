<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Elasticsearch\Search\Queries\Query;
use Generator;

readonly class NestedSort
{
    public function __construct(
        private string $path,
        private ?Query $query = null,
        private ?NestedSort $nestedSort = null,
    ) {
    }

    public function toArray(): Generator
    {
        $payload = [
            'path' => $this->path,
        ];

        if ($this->query) {
            $payload['filter'] = iterator_to_array($this->query->toArray());
        }

        if ($this->nestedSort) {
            $payload = array_merge($payload, iterator_to_array($this->nestedSort->toArray()));
        }

        yield 'nested' => $payload;
    }
}
