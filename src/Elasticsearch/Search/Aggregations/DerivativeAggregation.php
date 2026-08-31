<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithGapPolicy;

/**
 * Rozdil metriky oproti predchozimu bucketu - kolik pribylo mezi obdobimi.
 * Da se pouzit jen uvnitr histogramu nebo date_histogramu.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-derivative-aggregation.html
 */
class DerivativeAggregation extends AbstractAggregation
{
    use WithGapPolicy;

    protected ?string $unit = null;

    public function __construct(
        string $name,
        private readonly string $bucketsPath,
    ) {
        $this->name = $name;
    }

    /**
     * Prepocte rozdil na zadanou jednotku (jen u date_histogramu), napr. "day".
     */
    public function unit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['buckets_path' => $this->bucketsPath];

        if (null !== $this->unit) {
            $parameters['unit'] = $this->unit;
        }
        $this->provideGapPolicy($parameters);

        return new ArrayCollection(['derivative' => $parameters]);
    }
}
