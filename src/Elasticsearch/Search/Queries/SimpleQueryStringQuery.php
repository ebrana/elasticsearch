<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\Operator;
use Elasticsearch\Search\Queries\Enums\SimpleQueryStringFlag;

/**
 * Unlike query_string it does not fail on a syntax error, it only ignores the nonsensical part -
 * which makes it the safer choice for user input.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
 */
readonly class SimpleQueryStringQuery implements Query
{
    /**
     * @param string[] $fields
     * @param SimpleQueryStringFlag[] $flags
     */
    public function __construct(
        private string $query,
        private array $fields = [],
        private array $flags = [],
        private ?Operator $default_operator = null,
        private ?string $analyzer = null,
        private ?string $minimum_should_match = null,
        private ?int $fuzzy_max_expansions = null,
        private ?int $fuzzy_prefix_length = null,
        private ?bool $fuzzy_transpositions = null,
        private ?bool $lenient = null,
        private ?bool $analyze_wildcard = null,
        private ?bool $auto_generate_synonyms_phrase_query = null,
        private ?string $quote_field_suffix = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['query' => $this->query];

        if ($this->fields) {
            $data['fields'] = $this->fields;
        }

        if ($this->flags) {
            $data['flags'] = implode(
                '|',
                array_map(static fn (SimpleQueryStringFlag $flag): string => $flag->value, $this->flags)
            );
        }

        if (null !== $this->default_operator) {
            $data['default_operator'] = $this->default_operator->value;
        }

        if (null !== $this->analyzer) {
            $data['analyzer'] = $this->analyzer;
        }

        if (null !== $this->minimum_should_match) {
            $data['minimum_should_match'] = $this->minimum_should_match;
        }

        if (null !== $this->fuzzy_max_expansions) {
            $data['fuzzy_max_expansions'] = $this->fuzzy_max_expansions;
        }

        if (null !== $this->fuzzy_prefix_length) {
            $data['fuzzy_prefix_length'] = $this->fuzzy_prefix_length;
        }

        if (null !== $this->fuzzy_transpositions) {
            $data['fuzzy_transpositions'] = $this->fuzzy_transpositions;
        }

        if (null !== $this->lenient) {
            $data['lenient'] = $this->lenient;
        }

        if (null !== $this->analyze_wildcard) {
            $data['analyze_wildcard'] = $this->analyze_wildcard;
        }

        if (null !== $this->auto_generate_synonyms_phrase_query) {
            $data['auto_generate_synonyms_phrase_query'] = $this->auto_generate_synonyms_phrase_query;
        }

        if (null !== $this->quote_field_suffix) {
            $data['quote_field_suffix'] = $this->quote_field_suffix;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['simple_query_string' => $data];
    }
}
