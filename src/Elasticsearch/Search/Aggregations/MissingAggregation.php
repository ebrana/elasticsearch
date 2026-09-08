<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

/**
 * A bucket of documents that are missing the given field (or have it null).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-missing-aggregation.html
 */
class MissingAggregation extends AbstractAggregation
{
    use WithAggregations;

    public function __construct(
        string $name,
        private readonly string $field,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): ArrayCollection
    {
        $data = ['missing' => ['field' => $this->field]];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
