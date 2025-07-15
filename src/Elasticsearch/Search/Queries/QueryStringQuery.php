<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\Operator;
use Generator;

class QueryStringQuery implements Query
{
    /** @var string[]|null */
    protected ?array $fields = null;
    protected bool $allow_leading_wildcard = true;
    protected bool $analyze_wildcard = false;
    protected bool $auto_generate_synonyms_phrase_query = true;
    protected float $boost = 1.0;
    protected Operator $default_operator = Operator::OR;
    protected bool $enable_position_increments = true;
    protected ?string $analyzer = null;
    protected ?string $fuzziness = null;
    protected ?string $default_field = null;
    protected int $fuzzy_max_expansions = 50;
    protected int $fuzzy_prefix_length = 0;
    protected bool $fuzzy_transpositions = true;
    protected bool $lenient = false;
    protected int $max_determinized_states = 10000;
    protected ?string $minimum_should_match = null;
    protected ?string $quote_analyzer = null;
    protected int $phrase_slop = 0;
    protected ?string $quote_field_suffix = null;
    protected ?string $rewrite = null;
    protected ?string $time_zone = null;

    public function __construct(
        protected string $query,
    ) {
    }

    public function field(string $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function setAllowLeadingWildcard(bool $allow_leading_wildcard): self
    {
        $this->allow_leading_wildcard = $allow_leading_wildcard;

        return $this;
    }

    public function setAnalyzeWildcard(bool $analyze_wildcard): self
    {
        $this->analyze_wildcard = $analyze_wildcard;

        return $this;
    }

    public function setAutoGenerateSynonymsPhraseQuery(bool $auto_generate_synonyms_phrase_query): self
    {
        $this->auto_generate_synonyms_phrase_query = $auto_generate_synonyms_phrase_query;

        return $this;
    }

    public function setBoost(float $boost): self
    {
        $this->boost = $boost;

        return $this;
    }

    public function setDefaultOperator(Operator $default_operator): self
    {
        $this->default_operator = $default_operator;

        return $this;
    }

    public function setEnablePositionIncrements(bool $enable_position_increments): self
    {
        $this->enable_position_increments = $enable_position_increments;

        return $this;
    }

    public function setAnalyzer(?string $analyzer): self
    {
        $this->analyzer = $analyzer;

        return $this;
    }

    public function setFuzziness(?string $fuzziness): self
    {
        $this->fuzziness = $fuzziness;

        return $this;
    }

    public function setDefaultField(?string $default_field): self
    {
        $this->default_field = $default_field;

        return $this;
    }

    public function setFuzzyMaxExpansions(int $fuzzy_max_expansions): self
    {
        $this->fuzzy_max_expansions = $fuzzy_max_expansions;

        return $this;
    }

    public function setFuzzyPrefixLength(int $fuzzy_prefix_length): self
    {
        $this->fuzzy_prefix_length = $fuzzy_prefix_length;

        return $this;
    }

    public function setFuzzyTranspositions(bool $fuzzy_transpositions): self
    {
        $this->fuzzy_transpositions = $fuzzy_transpositions;

        return $this;
    }

    public function setLenient(bool $lenient): self
    {
        $this->lenient = $lenient;

        return $this;
    }

    public function setMaxDeterminizedStates(int $max_determinized_states): self
    {
        $this->max_determinized_states = $max_determinized_states;

        return $this;
    }

    public function setMinimumShouldMatch(?string $minimum_should_match): self
    {
        $this->minimum_should_match = $minimum_should_match;

        return $this;
    }

    public function setQuoteAnalyzer(?string $quote_analyzer): self
    {
        $this->quote_analyzer = $quote_analyzer;

        return $this;
    }

    public function setPhraseSlop(int $phrase_slop): self
    {
        $this->phrase_slop = $phrase_slop;

        return $this;
    }

    public function setQuoteFieldSuffix(?string $quote_field_suffix): self
    {
        $this->quote_field_suffix = $quote_field_suffix;

        return $this;
    }

    public function setRewrite(?string $rewrite): self
    {
        $this->rewrite = $rewrite;

        return $this;
    }

    public function setTimeZone(?string $time_zone): self
    {
        $this->time_zone = $time_zone;

        return $this;
    }

    public function toArray(): Generator
    {
        $body = [
            'query' => $this->query,
        ];

        if ($this->default_field) {
            $body['default_field'] = $this->default_field;
        }

        if (false === $this->allow_leading_wildcard) {
            $body['allow_leading_wildcard'] = $this->allow_leading_wildcard;
        }

        if ($this->analyze_wildcard) {
            $body['analyze_wildcard'] = $this->analyze_wildcard;
        }

        if ($this->analyzer) {
            $body['analyzer'] = $this->analyzer;
        }

        if (false === $this->auto_generate_synonyms_phrase_query) {
            $body['auto_generate_synonyms_phrase_query'] = $this->auto_generate_synonyms_phrase_query;
        }

        if ($this->boost !== 1.0) {
            $body['boost'] = $this->boost;
        }

        if (Operator::AND === $this->default_operator) {
            $body['default_operator'] = $this->default_operator->value;
        }

        if (false === $this->enable_position_increments) {
            $body['enable_position_increments'] = $this->enable_position_increments;
        }

        if ($this->fields) {
            $body['fields'] = $this->fields;
        }

        if ($this->fuzziness) {
            $body['fuzziness'] = $this->fuzziness;
        }

        if ($this->fuzzy_max_expansions !== 50) {
            $body['fuzzy_max_expansions'] = $this->fuzzy_max_expansions;
        }

        if ($this->fuzzy_prefix_length !== 0) {
            $body['fuzzy_prefix_length'] = $this->fuzzy_prefix_length;
        }

        if (false === $this->fuzzy_transpositions) {
            $body['fuzzy_transpositions'] = $this->fuzzy_transpositions;
        }

        if (false === $this->lenient) {
            $body['lenient'] = $this->lenient;
        }

        if ($this->max_determinized_states !== 10000) {
            $body['max_determinized_states'] = $this->max_determinized_states;
        }

        if ($this->minimum_should_match) {
            $body['minimum_should_match'] = $this->minimum_should_match;
        }

        if ($this->quote_analyzer) {
            $body['quote_analyzer'] = $this->quote_analyzer;
        }

        if ($this->phrase_slop !== 0) {
            $body['phrase_slop'] = $this->phrase_slop;
        }

        if ($this->quote_field_suffix) {
            $body['quote_field_suffix'] = $this->quote_field_suffix;
        }

        if ($this->rewrite) {
            $body['rewrite'] = $this->rewrite;
        }

        if ($this->time_zone) {
            $body['time_zone'] = $this->time_zone;
        }

        yield 'query_string' => $body;
    }
}
