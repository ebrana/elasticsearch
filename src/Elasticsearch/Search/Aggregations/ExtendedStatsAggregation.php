<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

/**
 * Jako stats, navic rozptyl, standardni odchylka a jeji hranice. `sigma` urcuje,
 * kolika odchylkami se hranice pocitaji (ES default 2).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-extendedstats-aggregation.html
 */
class ExtendedStatsAggregation extends AbstractAggregation
{
    use WithMissing;

    protected ?float $sigma = null;

    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
    }

    public function sigma(float $sigma): self
    {
        $this->sigma = $sigma;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['field' => $this->field];

        if (null !== $this->sigma) {
            $parameters['sigma'] = $this->sigma;
        }

        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return new ArrayCollection(['extended_stats' => $parameters]);
    }
}
