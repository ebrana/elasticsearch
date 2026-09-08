<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

/**
 * Returns count, min, max, avg and sum in a single request - cheaper than four separate aggregations.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-stats-aggregation.html
 */
class StatsAggregation extends AbstractAggregation
{
    use WithMissing;

    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['field' => $this->field];

        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return new ArrayCollection(['stats' => $parameters]);
    }
}
