<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

class GlobalAggregation extends AbstractAggregation
{
    use WithAggregations;

    public function __construct(
        string $name,
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection();
    }

    public function payload(): ArrayCollection
    {
        if (!$this->aggregations->isEmpty()) {
            $aggregation['aggs'] = iterator_to_array($this->aggregations->toArray());
        }

        return new ArrayCollection([
            'global' => (object)[],
            'aggs' => iterator_to_array($this->aggregations->toArray())
        ]);
    }
}
