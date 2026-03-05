<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

readonly class MatchAllQuery implements Query
{
    public function toArray(): array
    {
        return ['match_all' => new \stdClass()];
    }
}
