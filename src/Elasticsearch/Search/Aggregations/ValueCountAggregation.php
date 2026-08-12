<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pocet hodnot v poli - na rozdil od cardinality nepocita unikatni, ale vsechny.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-valuecount-aggregation.html
 */
class ValueCountAggregation extends AbstractAggregation
{
    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        return new ArrayCollection(['value_count' => ['field' => $this->field]]);
    }
}
