<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;

/**
 * Prepocita skore shod vlastnim skriptem. Skript se zadava tak, jak ho ceka Elasticsearch,
 * tedy vcetne klice `source` a pripadneho `params`.
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
