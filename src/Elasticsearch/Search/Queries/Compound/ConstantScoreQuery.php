<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound;

use Elasticsearch\Search\Queries\Query;

/**
 * Wraps a filter and gives all matches the same score - Elasticsearch does not have to compute
 * relevance and can cache the result.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html
 */
readonly class ConstantScoreQuery implements Query
{
    public function __construct(
        private Query $filter,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['filter' => $this->filter->toArray()];

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['constant_score' => $data];
    }
}
