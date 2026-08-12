<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Rozpada tokeny na hranicich nealfanumerickych znaku, zmen velikosti pismen a cislic -
 * typicky pro katalogova cisla ("Wi-Fi500" -> "Wi", "Fi", "500").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-word-delimiter-graph-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class WordDelimiterGraphFilter extends AbstractFilter
{
    /**
     * @param string[]|null $protected_words
     * @param string[]|null $type_table
     */
    public function __construct(
        string $name,
        private bool $adjust_offsets = true,
        private bool $catenate_all = false,
        private bool $catenate_numbers = false,
        private bool $catenate_words = false,
        private bool $generate_number_parts = true,
        private bool $generate_word_parts = true,
        private bool $ignore_keywords = false,
        private bool $preserve_original = false,
        private bool $split_on_case_change = true,
        private bool $split_on_numerics = true,
        private bool $stem_english_possessive = true,
        private ?array $protected_words = null,
        private ?string $protected_words_path = null,
        private ?array $type_table = null,
        private ?string $type_table_path = null,
    ) {
        parent::__construct($name, 'word_delimiter_graph');
    }

    public function isAdjustOffsets(): bool
    {
        return $this->adjust_offsets;
    }

    public function setAdjustOffsets(bool $adjust_offsets): void
    {
        $this->adjust_offsets = $adjust_offsets;
    }

    public function isCatenateAll(): bool
    {
        return $this->catenate_all;
    }

    public function setCatenateAll(bool $catenate_all): void
    {
        $this->catenate_all = $catenate_all;
    }

    public function isCatenateNumbers(): bool
    {
        return $this->catenate_numbers;
    }

    public function setCatenateNumbers(bool $catenate_numbers): void
    {
        $this->catenate_numbers = $catenate_numbers;
    }

    public function isCatenateWords(): bool
    {
        return $this->catenate_words;
    }

    public function setCatenateWords(bool $catenate_words): void
    {
        $this->catenate_words = $catenate_words;
    }

    public function isGenerateNumberParts(): bool
    {
        return $this->generate_number_parts;
    }

    public function setGenerateNumberParts(bool $generate_number_parts): void
    {
        $this->generate_number_parts = $generate_number_parts;
    }

    public function isGenerateWordParts(): bool
    {
        return $this->generate_word_parts;
    }

    public function setGenerateWordParts(bool $generate_word_parts): void
    {
        $this->generate_word_parts = $generate_word_parts;
    }

    public function isIgnoreKeywords(): bool
    {
        return $this->ignore_keywords;
    }

    public function setIgnoreKeywords(bool $ignore_keywords): void
    {
        $this->ignore_keywords = $ignore_keywords;
    }

    public function isPreserveOriginal(): bool
    {
        return $this->preserve_original;
    }

    public function setPreserveOriginal(bool $preserve_original): void
    {
        $this->preserve_original = $preserve_original;
    }

    public function isSplitOnCaseChange(): bool
    {
        return $this->split_on_case_change;
    }

    public function setSplitOnCaseChange(bool $split_on_case_change): void
    {
        $this->split_on_case_change = $split_on_case_change;
    }

    public function isSplitOnNumerics(): bool
    {
        return $this->split_on_numerics;
    }

    public function setSplitOnNumerics(bool $split_on_numerics): void
    {
        $this->split_on_numerics = $split_on_numerics;
    }

    public function isStemEnglishPossessive(): bool
    {
        return $this->stem_english_possessive;
    }

    public function setStemEnglishPossessive(bool $stem_english_possessive): void
    {
        $this->stem_english_possessive = $stem_english_possessive;
    }

    /**
     * @return string[]|null
     */
    public function getProtectedWords(): ?array
    {
        return $this->protected_words;
    }

    public function addProtectedWord(string $word): void
    {
        if (null === $this->protected_words) {
            $this->protected_words = [];
        }
        $this->protected_words[] = $word;
    }

    public function getProtectedWordsPath(): ?string
    {
        return $this->protected_words_path;
    }

    public function setProtectedWordsPath(?string $protected_words_path): void
    {
        $this->protected_words_path = $protected_words_path;
    }

    /**
     * @return string[]|null
     */
    public function getTypeTable(): ?array
    {
        return $this->type_table;
    }

    public function getTypeTablePath(): ?string
    {
        return $this->type_table_path;
    }

    public function setTypeTablePath(?string $type_table_path): void
    {
        $this->type_table_path = $type_table_path;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        // posilaji se jen odchylky od defaultu Elasticsearche
        foreach ([
            'adjust_offsets'          => [$this->adjust_offsets, true],
            'catenate_all'            => [$this->catenate_all, false],
            'catenate_numbers'        => [$this->catenate_numbers, false],
            'catenate_words'          => [$this->catenate_words, false],
            'generate_number_parts'   => [$this->generate_number_parts, true],
            'generate_word_parts'     => [$this->generate_word_parts, true],
            'ignore_keywords'         => [$this->ignore_keywords, false],
            'preserve_original'       => [$this->preserve_original, false],
            'split_on_case_change'    => [$this->split_on_case_change, true],
            'split_on_numerics'       => [$this->split_on_numerics, true],
            'stem_english_possessive' => [$this->stem_english_possessive, true],
        ] as $key => [$value, $default]) {
            if ($value !== $default) {
                $data[$key] = $value;
            }
        }

        if ($this->protected_words) {
            $data['protected_words'] = $this->protected_words;
        }

        if ($this->protected_words_path) {
            $data['protected_words_path'] = $this->protected_words_path;
        }

        if ($this->type_table) {
            $data['type_table'] = $this->type_table;
        }

        if ($this->type_table_path) {
            $data['type_table_path'] = $this->type_table_path;
        }

        return $data;
    }
}
