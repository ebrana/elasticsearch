<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use RuntimeException;

/**
 * A bucket per each combination of values from several fields. It is more expensive than nested
 * terms aggregations, but it also returns combinations that would be lost when `size` cuts
 * the higher level.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-multi-terms-aggregation.html
 */
class MultiTermsAggregation extends AbstractAggregation
{
    use WithAggregations;

    /** @var string[] */
    protected array $fields = [];

    protected ?int $size = null;
    protected ?int $shardSize = null;
    protected ?int $minDocCount = null;

    /** @var array<string, string>|null */
    protected ?array $order = null;

    public function __construct(
        string $name,
        string ...$fields
    ) {
        $this->name = $name;
        $this->fields = $fields;
        $this->aggregations = new AggregationCollection();
    }

    public function field(string $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function shardSize(int $shardSize): self
    {
        $this->shardSize = $shardSize;

        return $this;
    }

    public function minDocCount(int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    /**
     * @param array<string, string> $order
     */
    public function order(array $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        if (count($this->fields) < 2) {
            throw new RuntimeException('Multi terms aggregation must define at least two fields.');
        }

        $parameters = [
            'terms' => array_map(static fn (string $field): array => ['field' => $field], $this->fields),
        ];

        if (null !== $this->size) {
            $parameters['size'] = $this->size;
        }
        if (null !== $this->shardSize) {
            $parameters['shard_size'] = $this->shardSize;
        }
        if (null !== $this->minDocCount) {
            $parameters['min_doc_count'] = $this->minDocCount;
        }
        if (null !== $this->order) {
            $parameters['order'] = $this->order;
        }

        $data = ['multi_terms' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
