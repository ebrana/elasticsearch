<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

/**
 * Hleda slova v presnem poradi vedle sebe. `slop` povoluje, kolik pozic smi byt mezi nimi.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase.html
 */
readonly class MatchPhraseQuery implements Query
{
    public function __construct(
        private string $field,
        private string $query,
        private ?string $analyzer = null,
        private ?int $slop = null,
        private ?string $zero_terms_query = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['query' => $this->query];

        if (null !== $this->analyzer) {
            $data['analyzer'] = $this->analyzer;
        }

        if (null !== $this->slop) {
            $data['slop'] = $this->slop;
        }

        if (null !== $this->zero_terms_query) {
            $data['zero_terms_query'] = $this->zero_terms_query;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['match_phrase' => [$this->field => $data]];
    }
}
