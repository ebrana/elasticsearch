<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

/**
 * Jako match_phrase, ale posledni slovo bere jako prefix - typicke pro "search as you type"
 * bez potreby edge_ngram indexu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query-phrase-prefix.html
 */
readonly class MatchPhrasePrefixQuery implements Query
{
    public function __construct(
        private string $field,
        private string $query,
        private ?string $analyzer = null,
        private ?int $max_expansions = null,
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

        if (null !== $this->max_expansions) {
            $data['max_expansions'] = $this->max_expansions;
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

        return ['match_phrase_prefix' => [$this->field => $data]];
    }
}
