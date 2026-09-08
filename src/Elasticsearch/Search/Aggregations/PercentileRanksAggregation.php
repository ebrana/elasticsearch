<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

/**
 * The inverse of percentiles - for the given values it returns what percentage of documents
 * is below them ("what percentage of products is under 500 CZK").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-percentile-rank-aggregation.html
 */
class PercentileRanksAggregation extends AbstractAggregation
{
    use WithMissing;

    protected ?bool $keyed = null;

    /**
     * @param float[] $values
     */
    public function __construct(
        string $name,
        private readonly string $field,
        private readonly array $values
    ) {
        $this->name = $name;
    }

    public function keyed(bool $keyed): self
    {
        $this->keyed = $keyed;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'field'  => $this->field,
            'values' => $this->values,
        ];

        if (null !== $this->keyed) {
            $parameters['keyed'] = $this->keyed;
        }

        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return new ArrayCollection(['percentile_ranks' => $parameters]);
    }
}
