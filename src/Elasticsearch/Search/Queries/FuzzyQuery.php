<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

/**
 * A term query tolerant to typos - it looks for terms within the given edit distance.
 * It works on the unanalyzed value, so it suits keyword fields.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html
 */
readonly class FuzzyQuery implements Query
{
    public function __construct(
        private string $field,
        private string $value,
        private ?string $fuzziness = null,
        private ?int $max_expansions = null,
        private ?int $prefix_length = null,
        private ?bool $transpositions = null,
        private ?string $rewrite = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['value' => $this->value];

        if (null !== $this->fuzziness) {
            $data['fuzziness'] = $this->fuzziness;
        }

        if (null !== $this->max_expansions) {
            $data['max_expansions'] = $this->max_expansions;
        }

        if (null !== $this->prefix_length) {
            $data['prefix_length'] = $this->prefix_length;
        }

        if (null !== $this->transpositions) {
            $data['transpositions'] = $this->transpositions;
        }

        if (null !== $this->rewrite) {
            $data['rewrite'] = $this->rewrite;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['fuzzy' => [$this->field => $data]];
    }
}
