<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

class Sort
{
    private ?string $missing = null;
    private ?string $unmappedType = null;

    public function __construct(
        private readonly string $field,
        private readonly SortDirection $order
    ) {
    }

    public function missing(string $missing): self
    {
        $this->missing = $missing;

        return $this;
    }

    public function unmappedType(string $unmappedType): self
    {
        $this->unmappedType = $unmappedType;

        return $this;
    }

    public function toArray(): Generator
    {
        $payload = [
            'order' => $this->order->value,
        ];

        if ($this->missing) {
            $payload['missing'] = $this->missing;
        }

        if ($this->unmappedType) {
            $payload['unmapped_type'] = $this->unmappedType;
        }

        yield $this->field => $payload;
    }
}
