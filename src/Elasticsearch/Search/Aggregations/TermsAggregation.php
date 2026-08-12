<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

class TermsAggregation extends AbstractAggregation
{
    use WithMissing;
    use WithAggregations;

    protected ?int $size = null;
    protected ?int $shardSize = null;
    protected ?int $minDocCount = null;

    /** @var string[]|string|null */
    protected array|string|null $include = null;

    /** @var string[]|string|null */
    protected array|string|null $exclude = null;

    /** @var array<string, string>|null */
    protected ?array $order = null;

    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection();
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Kolik termu si vyzada z kazdeho shardu. Vyssi hodnota zpresnuje doc_count
     * u vetsiho poctu shardu za cenu pameti.
     */
    public function shardSize(int $shardSize): self
    {
        $this->shardSize = $shardSize;

        return $this;
    }

    /**
     * Vynecha termy s mensim poctem dokumentu (ES default 1).
     */
    public function minDocCount(int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    /**
     * Vyctem hodnot, nebo regulernim vyrazem.
     *
     * @param string[]|string $include
     */
    public function include(array|string $include): self
    {
        $this->include = $include;

        return $this;
    }

    /**
     * @param string[]|string $exclude
     */
    public function exclude(array|string $exclude): self
    {
        $this->exclude = $exclude;

        return $this;
    }

    /**
     * @param array<string, string> $order
     * @return $this
     */
    public function order(array $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'field' => $this->field,
        ];

        if ($this->size) {
            $parameters['size'] = $this->size;
        }

        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        if ($this->order) {
            $parameters['order'] = $this->order;
        }

        if (null !== $this->shardSize) {
            $parameters['shard_size'] = $this->shardSize;
        }

        if (null !== $this->minDocCount) {
            $parameters['min_doc_count'] = $this->minDocCount;
        }

        if (null !== $this->include) {
            $parameters['include'] = $this->include;
        }

        if (null !== $this->exclude) {
            $parameters['exclude'] = $this->exclude;
        }

        $aggregation = [
            'terms' => $parameters,
        ];

        if (!$this->aggregations->isEmpty()) {
            $aggregation['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($aggregation);
    }
}
