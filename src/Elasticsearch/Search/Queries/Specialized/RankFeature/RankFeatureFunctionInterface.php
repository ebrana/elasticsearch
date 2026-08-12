<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized\RankFeature;

/**
 * Funkce urcujici, jak se hodnota rank_feature pole prepocita na skore.
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
