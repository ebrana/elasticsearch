<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->aggregations as $aggregation) {
            $result[$aggregation->getName()] = $aggregation->toArray();
        }

        return $result;
    }
}
