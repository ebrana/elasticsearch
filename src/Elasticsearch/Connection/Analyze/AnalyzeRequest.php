<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Analyze;

/**
 * Telo requestu pro _analyze API. Slouzi k ladeni analyzeru - ukaze, na jake tokeny
 * Elasticsearch rozpada zadany text.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-analyze.html
 */
final class AnalyzeRequest
{
    /** @var string[] */
    private array $text;

    /**
     * @param string[]|string $text
     * @param array<int, string|array<string, mixed>> $filter
     * @param array<int, string|array<string, mixed>> $charFilter
     * @param string[] $attributes
     */
    public function __construct(
        array|string $text,
        private readonly ?string $analyzer = null,
        private readonly ?string $field = null,
        private readonly ?string $tokenizer = null,
        private readonly array $filter = [],
        private readonly array $charFilter = [],
        private readonly ?string $normalizer = null,
        private readonly bool $explain = false,
        private readonly array $attributes = [],
    ) {
        $this->text = is_array($text) ? $text : [$text];
    }

    /**
     * @return string[]
     */
    public function getText(): array
    {
        return $this->text;
    }

    public function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getTokenizer(): ?string
    {
        return $this->tokenizer;
    }

    /**
     * @return array<int, string|array<string, mixed>>
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @return array<int, string|array<string, mixed>>
     */
    public function getCharFilter(): array
    {
        return $this->charFilter;
    }

    public function getNormalizer(): ?string
    {
        return $this->normalizer;
    }

    public function isExplain(): bool
    {
        return $this->explain;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $body = ['text' => $this->text];

        if ($this->analyzer) {
            $body['analyzer'] = $this->analyzer;
        }

        if ($this->field) {
            $body['field'] = $this->field;
        }

        if ($this->normalizer) {
            $body['normalizer'] = $this->normalizer;
        }

        if ($this->tokenizer) {
            $body['tokenizer'] = $this->tokenizer;
        }

        if ($this->filter) {
            $body['filter'] = $this->filter;
        }

        if ($this->charFilter) {
            $body['char_filter'] = $this->charFilter;
        }

        if ($this->explain) {
            $body['explain'] = true;
        }

        if ($this->attributes) {
            $body['attributes'] = $this->attributes;
        }

        return $body;
    }
}
