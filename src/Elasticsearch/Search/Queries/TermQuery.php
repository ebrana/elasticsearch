<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class TermQuery implements Query
{
    public function __construct(
        private string $field,
        private string|bool|int|float $value
    ) {
    }

    public function toArray(): Generator
    {
        yield 'term' => [
            $this->field => $this->value,
        ];
    }
}
