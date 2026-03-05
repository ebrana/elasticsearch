<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Elasticsearch\Search\Queries\Query;

readonly class NestedSort
{
    public function __construct(
        private string $path,
        private ?Query $query = null,
        private ?NestedSort $nestedSort = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'path' => $this->path,
        ];

        if ($this->query) {
            $payload['filter'] = $this->query->toArray();
        }

        if ($this->nestedSort) {
            $payload = array_merge($payload, $this->nestedSort->toArray());
        }

        return ['nested' => $payload];
    }
}
