<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * The function determining how a rank_feature field value is turned into a score.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-rank-feature-query.html
 */
interface RankFeatureFunctionInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
