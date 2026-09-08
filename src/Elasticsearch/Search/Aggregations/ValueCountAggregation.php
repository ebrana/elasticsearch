<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * The number of values in a field - unlike cardinality it does not count unique ones, but all of them.
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
