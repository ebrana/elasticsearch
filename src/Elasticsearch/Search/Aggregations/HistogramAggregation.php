<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

/**
 * Rozdeli ciselne hodnoty do intervalu pevne sirky - typicky cenova pasma.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-histogram-aggregation.html
 */
class HistogramAggregation extends AbstractAggregation
{
    use WithAggregations;
    use WithMissing;

    protected ?int $minDocCount = null;
    protected ?float $offset = null;
    protected ?bool $keyed = null;

    /** @var array<string, float>|null */
    protected ?array $extendedBounds = null;

    /** @var array<string, float>|null */
    protected ?array $hardBounds = null;

    /** @var array<string, string>|null */
    protected ?array $order = null;

    public function __construct(
        string $name,
        private readonly string $field,
        private readonly float $interval,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    /**
     * 0 vrati i prazdne intervaly (ES default 1).
     */
    public function minDocCount(int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    public function offset(float $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function keyed(bool $keyed): self
    {
        $this->keyed = $keyed;

        return $this;
    }

    /**
     * Vynuti intervaly i mimo rozsah dat; funguje jen s minDocCount(0).
     */
    public function extendedBounds(float $min, float $max): self
    {
        $this->extendedBounds = ['min' => $min, 'max' => $max];

        return $this;
    }

    /**
     * Naopak intervaly mimo rozsah orizne.
     */
    public function hardBounds(float $min, float $max): self
    {
        $this->hardBounds = ['min' => $min, 'max' => $max];

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
        $parameters = [
            'field'    => $this->field,
            'interval' => $this->interval,
        ];

        if (null !== $this->minDocCount) {
            $parameters['min_doc_count'] = $this->minDocCount;
        }
        if (null !== $this->offset) {
            $parameters['offset'] = $this->offset;
        }
        if (null !== $this->keyed) {
            $parameters['keyed'] = $this->keyed;
        }
        if (null !== $this->extendedBounds) {
            $parameters['extended_bounds'] = $this->extendedBounds;
        }
        if (null !== $this->hardBounds) {
            $parameters['hard_bounds'] = $this->hardBounds;
        }
        if (null !== $this->order) {
            $parameters['order'] = $this->order;
        }
        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        $data = ['histogram' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
