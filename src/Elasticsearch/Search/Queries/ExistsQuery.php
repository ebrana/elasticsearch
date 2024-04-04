<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class ExistsQuery implements Query
{
    public function __construct(private string $field)
    {
    }

    public function toArray(): Generator
    {
        yield 'exists' => [
            'field' => $this->field
        ];
    }
}
