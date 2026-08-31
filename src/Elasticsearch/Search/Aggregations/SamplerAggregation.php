<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

/**
 * Omezi podagregace na nejlepsich `shard_size` dokumentu z kazdeho shardu - pouziva se
 * jako levnejsi obal kolem significant_terms.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-sampler-aggregation.html
 */
class SamplerAggregation extends AbstractAggregation
{
    use WithAggregations;

    protected ?int $shardSize = null;

    public function __construct(
        string $name,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function shardSize(int $shardSize): self
    {
        $this->shardSize = $shardSize;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [];
        if (null !== $this->shardSize) {
            $parameters['shard_size'] = $this->shardSize;
        }

        // bez parametru musi jit do JSONu prazdny objekt, ne prazdne pole
        $data = ['sampler' => [] === $parameters ? (object)[] : $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
