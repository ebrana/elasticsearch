<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Queries\Query;
use RuntimeException;

/**
 * Vic pojmenovanych filtru naraz - kazdy dostane svuj bucket. Na rozdil od FilterAggregation
 * (jednotne cislo) nemusi clovek posilat samostatnou agregaci pro kazdou podminku.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filters-aggregation.html
 */
class FiltersAggregation extends AbstractAggregation
{
    use WithAggregations;

    /** @var array<string, Query> */
    protected array $filters = [];

    protected ?bool $otherBucket = null;
    protected ?string $otherBucketKey = null;

    /**
     * @param array<string, Query> $filters
     */
    public function __construct(
        string $name,
        array $filters = []
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection();

        foreach ($filters as $key => $filter) {
            $this->filter((string)$key, $filter);
        }
    }

    public function filter(string $key, Query $query): self
    {
        $this->filters[$key] = $query;

        return $this;
    }

    /**
     * Prida bucket pro dokumenty, ktere neodpovidaji zadnemu filtru.
     */
    public function otherBucket(bool $otherBucket, ?string $key = null): self
    {
        $this->otherBucket = $otherBucket;
        $this->otherBucketKey = $key;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        if ([] === $this->filters) {
            throw new RuntimeException('Filters aggregation must define at least one filter.');
        }

        $filters = [];
        foreach ($this->filters as $key => $query) {
            $filters[$key] = $query->toArray();
        }

        $parameters = ['filters' => $filters];

        if (null !== $this->otherBucket) {
            $parameters['other_bucket'] = $this->otherBucket;
        }
        if (null !== $this->otherBucketKey) {
            $parameters['other_bucket_key'] = $this->otherBucketKey;
        }

        $data = ['filters' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
