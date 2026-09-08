<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

use Elasticsearch\Search\Suggest\Enums\StringDistance;
use Elasticsearch\Search\Suggest\Enums\SuggestMode;

/**
 * A candidate generator for PhraseSuggest. Without it Elasticsearch uses a single default one
 * over the field of the phrase suggester.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#phrase-suggester
 */
readonly class DirectGenerator
{
    public function __construct(
        private string $field,
        private ?int $size = null,
        private ?SuggestMode $suggest_mode = null,
        private ?int $max_edits = null,
        private ?int $prefix_length = null,
        private ?int $min_word_length = null,
        private ?int $max_inspections = null,
        private ?float $min_doc_freq = null,
        private ?float $max_term_freq = null,
        private ?StringDistance $string_distance = null,
        private ?string $pre_filter = null,
        private ?string $post_filter = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['field' => $this->field];

        $options = [
            'size'            => $this->size,
            'suggest_mode'    => $this->suggest_mode?->value,
            'max_edits'       => $this->max_edits,
            'prefix_length'   => $this->prefix_length,
            'min_word_length' => $this->min_word_length,
            'max_inspections' => $this->max_inspections,
            'min_doc_freq'    => $this->min_doc_freq,
            'max_term_freq'   => $this->max_term_freq,
            'string_distance' => $this->string_distance?->value,
            'pre_filter'      => $this->pre_filter,
            'post_filter'     => $this->post_filter,
        ];

        foreach ($options as $key => $value) {
            if (null !== $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
