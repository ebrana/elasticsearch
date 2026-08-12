<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Compound;

use Elasticsearch\Search\Queries\Query;

/**
 * Dokumenty odpovidajici `negative` nevyradi, jen jim snizi skore. Hodi se, kdyz
 * neco nechceme uplne skryt, jen odsunout dozadu (napr. nedostupne produkty).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html
 */
readonly class BoostingQuery implements Query
{
    public function __construct(
        private Query $positive,
        private Query $negative,
        private float $negative_boost,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'positive'       => $this->positive->toArray(),
            'negative'       => $this->negative->toArray(),
            'negative_boost' => $this->negative_boost,
        ];

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['boosting' => $data];
    }
}
