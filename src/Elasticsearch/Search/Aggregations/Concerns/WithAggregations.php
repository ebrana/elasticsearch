<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Concerns;

use Elasticsearch\Search\Aggregations\AbstractAggregation;
use Elasticsearch\Search\Aggregations\AggregationCollection;

trait WithAggregations
{
    protected AggregationCollection $aggregations;

    public function aggregation(AbstractAggregation $aggregation): self
    {
        $this->aggregations->add($aggregation);

        return $this;
    }
}
