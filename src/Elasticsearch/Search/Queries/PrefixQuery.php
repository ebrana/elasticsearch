<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class PrefixQuery implements Query
{
    public function __construct(
        private string $field,
        private string $query
    ) {
    }

    public function toArray(): Generator
    {
        yield 'prefix' => [
            $this->field => [
                'value' => $this->query,
            ],
        ];
    }
}
