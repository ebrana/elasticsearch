<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;
use Elasticsearch\Search\Queries\Specialized\RankFeature\RankFeatureFunctionInterface;

/**
 * Raises the score by the value of a rank_feature field (e.g. popularity, number of sales).
 * With no function given Elasticsearch uses saturation with a computed pivot.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-rank-feature-query.html
 */
readonly class RankFeatureQuery implements Query
{
    public function __construct(
        private string $field,
        private ?RankFeatureFunctionInterface $function = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['field' => $this->field];

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        if (null !== $this->function) {
            $data = array_merge($data, $this->function->toArray());
        }

        return ['rank_feature' => $data];
    }
}
