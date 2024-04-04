<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

class NestedAggregation extends AbstractAggregation
{
    use WithAggregations;

    public function __construct(
        string $name,
        private readonly string $path,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): ArrayCollection
    {
        return new ArrayCollection([
            'nested' => [
                'path' => $this->path,
            ],
            'aggs'   => iterator_to_array($this->aggregations->toArray()),
        ]);
    }
}
