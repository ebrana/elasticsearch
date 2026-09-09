<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;

/**
 * Recomputes the score of the matches with a custom script. The script is passed the way
 * Elasticsearch expects it, i.e. including the `source` key and an optional `params`.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-script-score-query.html
 */
readonly class ScriptScoreQuery implements Query
{
    /**
     * @param array<string, mixed> $script
     */
    public function __construct(
        private Query $query,
        private array $script,
        private ?float $min_score = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'query'  => $this->query->toArray(),
            'script' => $this->script,
        ];

        if (null !== $this->min_score) {
            $data['min_score'] = $this->min_score;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['script_score' => $data];
    }
}
