<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Generator;

class AggregationCollection
{
    /** @var AbstractAggregation[] */
    protected array $aggregations;

    public function __construct(AbstractAggregation ...$aggregations)
    {
        $this->aggregations = $aggregations;
    }

    public function add(AbstractAggregation $aggregation): self
    {
        $this->aggregations[] = $aggregation;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->aggregations);
    }

    public function toArray(): Generator
    {
        foreach ($this->aggregations as $aggregation) {
            yield $aggregation->getName() => $aggregation->toArray()->current();
        }
    }
}
