<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Rescore;

use Elasticsearch\Search\Queries\Query;
use Elasticsearch\Search\Rescore\Enums\RescoreMode;

/**
 * Recomputes the score of only the first `window_size` results from each shard. Useful for
 * expensive queries (e.g. match_phrase or script) that would not pay off across the whole index.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/filter-search-results.html#rescore
 */
readonly class Rescore
{
    public function __construct(
        private Query $rescore_query,
        private ?int $window_size = null,
        private ?float $query_weight = null,
        private ?float $rescore_query_weight = null,
        private ?RescoreMode $score_mode = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $query = ['rescore_query' => $this->rescore_query->toArray()];

        if (null !== $this->query_weight) {
            $query['query_weight'] = $this->query_weight;
        }

        if (null !== $this->rescore_query_weight) {
            $query['rescore_query_weight'] = $this->rescore_query_weight;
        }

        if (null !== $this->score_mode) {
            $query['score_mode'] = $this->score_mode->value;
        }

        $data = [];
        if (null !== $this->window_size) {
            $data['window_size'] = $this->window_size;
        }
        $data['query'] = $query;

        return $data;
    }
}
