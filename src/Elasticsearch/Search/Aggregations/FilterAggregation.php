<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Queries\Query;

class FilterAggregation extends AbstractAggregation
{
    use WithAggregations;

    public function __construct(
        string $name,
        private readonly Query $filter,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): ArrayCollection
    {
        return new ArrayCollection([
            'filter' => iterator_to_array($this->filter->toArray()),
            'aggs'   => iterator_to_array($this->aggregations->toArray()),
        ]);
    }
}
