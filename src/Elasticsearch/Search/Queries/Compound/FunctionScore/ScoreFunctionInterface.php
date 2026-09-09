<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound\FunctionScore;

/**
 * Jedna polozka v `functions` u function_score query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
 */
interface ScoreFunctionInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
