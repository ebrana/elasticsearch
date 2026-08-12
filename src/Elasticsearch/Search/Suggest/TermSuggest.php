<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

use Elasticsearch\Search\Suggest\Enums\StringDistance;
use Elasticsearch\Search\Suggest\Enums\SuggestMode;
use Elasticsearch\Search\Suggest\Enums\SuggestSort;

/**
 * Navrhy po jednotlivych slovech - "nemyslel jste tim...?" na urovni termu.
 * Pro celou frazi je vhodnejsi PhraseSuggest.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#term-suggester
 */
readonly class TermSuggest implements SuggestInterface
{
    public function __construct(
        private string $name,
        private string $text,
        private string $field,
        private ?string $analyzer = null,
        private ?int $size = null,
        private ?SuggestSort $sort = null,
        private ?SuggestMode $suggest_mode = null,
        private ?int $max_edits = null,
        private ?int $prefix_length = null,
        private ?int $min_word_length = null,
        private ?int $max_inspections = null,
        private ?float $min_doc_freq = null,
        private ?float $max_term_freq = null,
        private ?StringDistance $string_distance = null,
        private ?int $shard_size = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $term = ['field' => $this->field];

        foreach ([
            'analyzer'        => $this->analyzer,
            'size'            => $this->size,
            'sort'            => $this->sort?->value,
            'suggest_mode'    => $this->suggest_mode?->value,
            'max_edits'       => $this->max_edits,
            'prefix_length'   => $this->prefix_length,
            'min_word_length' => $this->min_word_length,
            'max_inspections' => $this->max_inspections,
            'min_doc_freq'    => $this->min_doc_freq,
            'max_term_freq'   => $this->max_term_freq,
            'string_distance' => $this->string_distance?->value,
            'shard_size'      => $this->shard_size,
        ] as $key => $value) {
            if (null !== $value) {
                $term[$key] = $value;
            }
        }

        return [
            'text' => $this->text,
            'term' => $term,
        ];
    }
}
