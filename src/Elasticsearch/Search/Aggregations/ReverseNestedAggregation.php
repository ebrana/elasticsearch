<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

class ReverseNestedAggregation extends AbstractAggregation
{
    use WithAggregations;

    public function __construct(
        string $name,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): ArrayCollection
    {
        return new ArrayCollection([
            'reverse_nested' => [],
            'aggs' => iterator_to_array($this->aggregations->toArray())
        ]);
    }
}
