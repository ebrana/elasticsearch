<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\Operator;
use Generator;

trait MatchQueryTrait
{
    protected ?string $analyzer = null;
    protected float $boost = 1.0;
    protected Operator $operator = Operator::OR;
    protected ?string $minimum_should_match = null;
    protected ?string $fuzziness = null;
    protected bool $lenient = false;
    protected ?int $prefix_length = null;
    protected ?int $max_expansions = null;
    protected ?string $zero_terms_query = null;
    protected ?string $fuzzy_rewrite = null;
    protected bool $auto_generate_synonyms_phrase_query = true;
    protected bool $fuzzy_transpositions = true;

    public function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    public function setAnalyzer(?string $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function getBoost(): float
    {
        return $this->boost;
    }

    public function setBoost(float $boost): void
    {
        $this->boost = $boost;
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): void
    {
        $this->operator = $operator;
    }

    public function getMinimumShouldMatch(): ?string
    {
        return $this->minimum_should_match;
    }

    public function setMinimumShouldMatch(?string $minimum_should_match): void
    {
        $this->minimum_should_match = $minimum_should_match;
    }

    public function getFuzziness(): ?string
    {
        return $this->fuzziness;
    }

    public function setFuzziness(?string $fuzziness): void
    {
        $this->fuzziness = $fuzziness;
    }

    public function isLenient(): bool
    {
        return $this->lenient;
    }

    public function setLenient(bool $lenient): void
    {
        $this->lenient = $lenient;
    }

    public function getPrefixLength(): ?int
    {
        return $this->prefix_length;
    }

    public function setPrefixLength(?int $prefix_length): void
    {
        $this->prefix_length = $prefix_length;
    }

    public function getMaxExpansions(): ?int
    {
        return $this->max_expansions;
    }

    public function setMaxExpansions(?int $max_expansions): void
    {
        $this->max_expansions = $max_expansions;
    }

    public function getZeroTermsQuery(): ?string
    {
        return $this->zero_terms_query;
    }

    public function setZeroTermsQuery(?string $zero_terms_query): void
    {
        $this->zero_terms_query = $zero_terms_query;
    }

    public function getFuzzyRewrite(): ?string
    {
        return $this->fuzzy_rewrite;
    }

    public function setFuzzyRewrite(?string $fuzzy_rewrite): void
    {
        $this->fuzzy_rewrite = $fuzzy_rewrite;
    }

    public function isAutoGenerateSynonymsPhraseQuery(): bool
    {
        return $this->auto_generate_synonyms_phrase_query;
    }

    public function setAutoGenerateSynonymsPhraseQuery(bool $auto_generate_synonyms_phrase_query): void
    {
        $this->auto_generate_synonyms_phrase_query = $auto_generate_synonyms_phrase_query;
    }

    public function isFuzzyTranspositions(): bool
    {
        return $this->fuzzy_transpositions;
    }

    public function setFuzzyTranspositions(bool $fuzzy_transpositions): void
    {
        $this->fuzzy_transpositions = $fuzzy_transpositions;
    }

    protected function toArray(): Generator
    {
        $body = [];

        if ($this->analyzer) {
            $body['analyzer'] = $this->analyzer;
        }

        if ($this->fuzziness) {
            $body['fuzziness'] = $this->fuzziness;
        }

        if ($this->minimum_should_match) {
            $body['minimum_should_match'] = $this->minimum_should_match;
        }

        if ($this->lenient) {
            $body['lenient'] = $this->lenient;
        }

        if ($this->prefix_length) {
            $body['prefix_length'] = $this->prefix_length;
        }

        if ($this->max_expansions) {
            $body['max_expansions'] = $this->max_expansions;
        }

        if ($this->zero_terms_query) {
            $body['zero_terms_query'] = $this->zero_terms_query;
        }

        if ($this->fuzzy_rewrite) {
            $body['fuzzy_rewrite'] = $this->fuzzy_rewrite;
        }

        $body['boost'] = $this->boost;
        $body['operator'] = $this->operator->value;
        $body['auto_generate_synonyms_phrase_query'] = $this->auto_generate_synonyms_phrase_query;
        $body['fuzzy_transpositions'] = $this->fuzzy_transpositions;

        yield $body;
    }
}
