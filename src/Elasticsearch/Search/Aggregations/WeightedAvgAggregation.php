<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Vazeny prumer - hodnota a vaha se berou z ruznych poli (napr. hodnoceni vazene
 * poctem recenzi). Na rozdil od ostatnich metrik ma `missing` zvlast pro hodnotu i vahu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-weight-avg-aggregation.html
 */
class WeightedAvgAggregation extends AbstractAggregation
{
    protected ?float $valueMissing = null;
    protected ?float $weightMissing = null;

    public function __construct(
        string $name,
        private readonly string $valueField,
        private readonly string $weightField
    ) {
        $this->name = $name;
    }

    public function valueMissing(float $missing): self
    {
        $this->valueMissing = $missing;

        return $this;
    }

    public function weightMissing(float $missing): self
    {
        $this->weightMissing = $missing;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $value = ['field' => $this->valueField];
        if (null !== $this->valueMissing) {
            $value['missing'] = $this->valueMissing;
        }

        $weight = ['field' => $this->weightField];
        if (null !== $this->weightMissing) {
            $weight['missing'] = $this->weightMissing;
        }

        return new ArrayCollection([
            'weighted_avg' => [
                'value'  => $value,
                'weight' => $weight,
            ],
        ]);
    }
}
