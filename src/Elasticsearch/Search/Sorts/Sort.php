<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

class Sort implements SortInterface
{
    private ?string $missing = null;
    private ?string $unmappedType = null;
    private ?string $format = null;
    private ?string $numeric_type = null;
    private ?NestedSort $nestedSort = null;
    private ?SortMode $mode = null;

    public function __construct(
        private readonly string $field,
        private readonly ?SortDirection $order = null,
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

    public function getMode(): ?SortMode
    {
        return $this->mode;
    }

    public function setMode(?SortMode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getNumericType(): ?string
    {
        return $this->numeric_type;
    }

    public function setNumericType(?string $numeric_type): self
    {
        $this->numeric_type = $numeric_type;

        return $this;
    }

    public function getNestedSort(): ?NestedSort
    {
        return $this->nestedSort;
    }

    public function setNestedSort(?NestedSort $nestedSort): self
    {
        $this->nestedSort = $nestedSort;

        return $this;
    }

    public function toArray(): Generator
    {
        $payload = [];

        if ($this->order) {
            $payload['order'] = $this->order->value;
        }

        if ($this->missing) {
            $payload['missing'] = $this->missing;
        }

        if ($this->unmappedType) {
            $payload['unmapped_type'] = $this->unmappedType;
        }

        if ($this->mode) {
            $payload['mode'] = $this->mode->value;
        }

        if ($this->format) {
            $payload['format'] = $this->format;
        }

        if ($this->numeric_type) {
            $payload['numeric_type'] = $this->numeric_type;
        }

        if ($this->nestedSort) {
            $payload = array_merge($payload, iterator_to_array($this->nestedSort->toArray()));
        }

        yield $this->field => $payload;
    }
}
