<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Postupny soucet metriky napric buckety - kumulativni krivka.
 * Da se pouzit jen uvnitr histogramu nebo date_histogramu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-cumulative-sum-aggregation.html
 */
class CumulativeSumAggregation extends AbstractAggregation
{
    protected ?string $format = null;

    public function __construct(
        string $name,
        private readonly string $bucketsPath,
    ) {
        $this->name = $name;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['buckets_path' => $this->bucketsPath];

        if (null !== $this->format) {
            $parameters['format'] = $this->format;
        }

        return new ArrayCollection(['cumulative_sum' => $parameters]);
    }
}
