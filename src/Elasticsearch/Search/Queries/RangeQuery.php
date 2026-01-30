<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

class RangeQuery implements Query
{
    private string|int|float|null $gte = null;
    private string|int|float|null $lt = null;
    private string|int|float|null $lte = null;
    private string|int|float|null $gt = null;

    public function __construct(
        private readonly string $field,
        private readonly ?float $boost = null,
    ) {
    }

    public function lt(string|int|float $value): self
    {
        $this->lt = $value;

        return $this;
    }

    public function lte(string|int|float $value): self
    {
        $this->lte = $value;

        return $this;
    }

    public function gt(string|int|float $value): self
    {
        $this->gt = $value;

        return $this;
    }

    public function gte(string|int|float $value): self
    {
        $this->gte = $value;

        return $this;
    }

    public function toArray(): Generator
    {
        $parameters = [];

        if ($this->lt !== null) {
            $parameters['lt'] = $this->lt;
        }

        if ($this->lte !== null) {
            $parameters['lte'] = $this->lte;
        }

        if ($this->gt !== null) {
            $parameters['gt'] = $this->gt;
        }

        if ($this->gte !== null) {
            $parameters['gte'] = $this->gte;
        }

        if ($this->boost) {
            $parameters['boost'] = $this->boost;
        }

        yield 'range' => [
            $this->field => $parameters,
        ];
    }
}
