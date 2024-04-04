<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class WildcardQuery implements Query
{
    public function __construct(
        private string $field,
        private string $value
    ) {
    }

    public function toArray(): Generator
    {
        yield 'wildcard' => [
            $this->field => [
                'value' => $this->value,
            ]
        ];
    }
}
