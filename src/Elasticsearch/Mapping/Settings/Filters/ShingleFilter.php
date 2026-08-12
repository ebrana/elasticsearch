<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Sklada n-tice po sobe jdoucich tokenu (slovni n-gramy) - pomaha pri hledani frazi.
 * Pozor na index.max_shingle_diff (default 3).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-shingle-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class ShingleFilter extends AbstractFilter
{
    public const int DEFAULT_SHINGLE_SIZE = 2;
    public const string DEFAULT_TOKEN_SEPARATOR = ' ';
    public const string DEFAULT_FILLER_TOKEN = '_';

    public function __construct(
        string $name,
        private int $min_shingle_size = self::DEFAULT_SHINGLE_SIZE,
        private int $max_shingle_size = self::DEFAULT_SHINGLE_SIZE,
        private bool $output_unigrams = true,
        private bool $output_unigrams_if_no_shingles = false,
        private string $token_separator = self::DEFAULT_TOKEN_SEPARATOR,
        private string $filler_token = self::DEFAULT_FILLER_TOKEN,
    ) {
        parent::__construct($name, 'shingle');
    }

    public function getMinShingleSize(): int
    {
        return $this->min_shingle_size;
    }

    public function setMinShingleSize(int $min_shingle_size): void
    {
        $this->min_shingle_size = $min_shingle_size;
    }

    public function getMaxShingleSize(): int
    {
        return $this->max_shingle_size;
    }

    public function setMaxShingleSize(int $max_shingle_size): void
    {
        $this->max_shingle_size = $max_shingle_size;
    }

    public function isOutputUnigrams(): bool
    {
        return $this->output_unigrams;
    }

    public function setOutputUnigrams(bool $output_unigrams): void
    {
        $this->output_unigrams = $output_unigrams;
    }

    public function isOutputUnigramsIfNoShingles(): bool
    {
        return $this->output_unigrams_if_no_shingles;
    }

    public function setOutputUnigramsIfNoShingles(bool $output_unigrams_if_no_shingles): void
    {
        $this->output_unigrams_if_no_shingles = $output_unigrams_if_no_shingles;
    }

    public function getTokenSeparator(): string
    {
        return $this->token_separator;
    }

    public function setTokenSeparator(string $token_separator): void
    {
        $this->token_separator = $token_separator;
    }

    public function getFillerToken(): string
    {
        return $this->filler_token;
    }

    public function setFillerToken(string $filler_token): void
    {
        $this->filler_token = $filler_token;
    }

    /**
     * @return array<string, array<string>|bool|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_SHINGLE_SIZE !== $this->min_shingle_size) {
            $data['min_shingle_size'] = $this->min_shingle_size;
        }

        if (self::DEFAULT_SHINGLE_SIZE !== $this->max_shingle_size) {
            $data['max_shingle_size'] = $this->max_shingle_size;
        }

        if (false === $this->output_unigrams) {
            $data['output_unigrams'] = false;
        }

        if ($this->output_unigrams_if_no_shingles) {
            $data['output_unigrams_if_no_shingles'] = true;
        }

        if (self::DEFAULT_TOKEN_SEPARATOR !== $this->token_separator) {
            $data['token_separator'] = $this->token_separator;
        }

        if (self::DEFAULT_FILLER_TOKEN !== $this->filler_token) {
            $data['filler_token'] = $this->filler_token;
        }

        return $data;
    }
}
