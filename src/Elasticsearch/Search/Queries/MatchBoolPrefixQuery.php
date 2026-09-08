<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\Operator;

/**
 * Searches every word as a term and the last one as a prefix - unlike match_phrase_prefix
 * the word order does not matter. A good choice for autocomplete over several words.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-bool-prefix-query.html
 */
readonly class MatchBoolPrefixQuery implements Query
{
    public function __construct(
        private string $field,
        private string $query,
        private ?string $analyzer = null,
        private ?Operator $operator = null,
        private ?string $minimum_should_match = null,
        private ?string $fuzziness = null,
        private ?int $prefix_length = null,
        private ?int $max_expansions = null,
        private ?bool $fuzzy_transpositions = null,
        private ?string $fuzzy_rewrite = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['query' => $this->query];

        if (null !== $this->analyzer) {
            $data['analyzer'] = $this->analyzer;
        }

        if (null !== $this->operator) {
            $data['operator'] = $this->operator->value;
        }

        if (null !== $this->minimum_should_match) {
            $data['minimum_should_match'] = $this->minimum_should_match;
        }

        if (null !== $this->fuzziness) {
            $data['fuzziness'] = $this->fuzziness;
        }

        if (null !== $this->prefix_length) {
            $data['prefix_length'] = $this->prefix_length;
        }

        if (null !== $this->max_expansions) {
            $data['max_expansions'] = $this->max_expansions;
        }

        if (null !== $this->fuzzy_transpositions) {
            $data['fuzzy_transpositions'] = $this->fuzzy_transpositions;
        }

        if (null !== $this->fuzzy_rewrite) {
            $data['fuzzy_rewrite'] = $this->fuzzy_rewrite;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['match_bool_prefix' => [$this->field => $data]];
    }
}
