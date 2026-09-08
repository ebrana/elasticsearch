<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

/**
 * Jeden interval pro range a date_range agregaci. Hranice `from` je vcetne, `to` uz ne.
 * Vynechana hranice znamena neomezeno.
 */
final readonly class Range
{
    public function __construct(
        private string|int|float|null $from = null,
        private string|int|float|null $to = null,
        private ?string $key = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if (null !== $this->key) {
            $data['key'] = $this->key;
        }

        if (null !== $this->from) {
            $data['from'] = $this->from;
        }

        if (null !== $this->to) {
            $data['to'] = $this->to;
        }

        return $data;
    }
}
