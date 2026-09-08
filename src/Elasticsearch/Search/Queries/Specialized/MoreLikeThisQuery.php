<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries\Specialized;

use Elasticsearch\Search\Queries\Query;

/**
 * Looks for documents similar to a given text or to existing documents - "similar products".
 * `like` can hold either a text, or a document reference shaped ['_index' => ..., '_id' => ...].
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html
 */
readonly class MoreLikeThisQuery implements Query
{
    /**
     * @param string[] $fields
     * @param array<int, string|array<string, mixed>>|string $like
     * @param array<int, string|array<string, mixed>>|string|null $unlike
     * @param string[]|string|null $stop_words
     */
    public function __construct(
        private array $fields,
        private array|string $like,
        private array|string|null $unlike = null,
        private ?int $min_term_freq = null,
        private ?int $max_query_terms = null,
        private ?int $min_doc_freq = null,
        private ?int $max_doc_freq = null,
        private ?int $min_word_length = null,
        private ?int $max_word_length = null,
        private array|string|null $stop_words = null,
        private ?string $analyzer = null,
        private int|string|null $minimum_should_match = null,
        private ?float $boost_terms = null,
        private ?bool $include = null,
        private ?bool $fail_on_unsupported_field = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'fields' => $this->fields,
            'like'   => $this->like,
        ];

        if (null !== $this->unlike) {
            $data['unlike'] = $this->unlike;
        }

        $options = [
            'min_term_freq'             => $this->min_term_freq,
            'max_query_terms'           => $this->max_query_terms,
            'min_doc_freq'              => $this->min_doc_freq,
            'max_doc_freq'              => $this->max_doc_freq,
            'min_word_length'           => $this->min_word_length,
            'max_word_length'           => $this->max_word_length,
            'stop_words'                => $this->stop_words,
            'analyzer'                  => $this->analyzer,
            'minimum_should_match'      => $this->minimum_should_match,
            'boost_terms'               => $this->boost_terms,
            'include'                   => $this->include,
            'fail_on_unsupported_field' => $this->fail_on_unsupported_field,
            'boost'                     => $this->boost,
        ];

        foreach ($options as $key => $value) {
            if (null !== $value) {
                $data[$key] = $value;
            }
        }

        return ['more_like_this' => $data];
    }
}
