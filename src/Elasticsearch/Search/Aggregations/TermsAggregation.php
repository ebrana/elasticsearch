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

        if ($this->missing) {
            $parameters['missing'] = $this->missing;
        }

        if ($this->order) {
            $parameters['order'] = $this->order;
        }

        $aggregation = [
            'terms' => $parameters,
        ];

        if (!$this->aggregations->isEmpty()) {
            $aggregation['aggs'] = iterator_to_array($this->aggregations->toArray());
        }

        return new ArrayCollection($aggregation);
    }
}
