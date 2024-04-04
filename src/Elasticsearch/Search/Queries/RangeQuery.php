<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

class RangeQuery implements Query
{
    private ?string $gte = null;
    private ?string $lt = null;
    private ?string $lte = null;
    private ?string $gt = null;

    public function __construct(
        private readonly string $field
    ) {
    }

    public function lt(string $value): self
    {
        $this->lt = $value;

        return $this;
    }

    public function lte(string $value): self
    {
        $this->lte = $value;

        return $this;
    }

    public function gt(string $value): self
    {
        $this->gt = $value;

        return $this;
    }

    public function gte(string $value): self
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

        yield 'range' => [
            $this->field => $parameters,
        ];
    }
}
