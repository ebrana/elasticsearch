<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

readonly class MatchAllQuery implements Query
{
    public function toArray(): Generator
    {
        // return 'match_all' => {}
        yield 'match_all' => new \stdClass();
    }
}
