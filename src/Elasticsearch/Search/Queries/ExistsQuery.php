<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

readonly class ExistsQuery implements Query
{
    public function __construct(private string $field)
    {
    }

    public function toArray(): array
    {
        return ['exists' => [
            'field' => $this->field
        ]];
    }
}
