<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;

/**
 * Percentily hodnot - typicky pro cenove filtry ("v jake cene je 95 % produktu").
 * Vysledek je priblizny; presnost roste s `compression`.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-percentile-aggregation.html
 */
class PercentilesAggregation extends AbstractAggregation
{
    use WithMissing;

    /** @var float[]|null */
    protected ?array $percents = null;

    protected ?bool $keyed = null;
    protected ?float $compression = null;

    public function __construct(
        string $name,
        private readonly string $field
    ) {
        $this->name = $name;
    }

    /**
     * @param float[] $percents
     */
    public function percents(array $percents): self
    {
        $this->percents = $percents;

        return $this;
    }

    /**
     * false vrati pole polozek misto objektu naklicovaneho percentilem.
     */
    public function keyed(bool $keyed): self
    {
        $this->keyed = $keyed;

        return $this;
    }

    public function compression(float $compression): self
    {
        $this->compression = $compression;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['field' => $this->field];

        if (null !== $this->percents) {
            $parameters['percents'] = $this->percents;
        }

        if (null !== $this->keyed) {
            $parameters['keyed'] = $this->keyed;
        }

        if (null !== $this->compression) {
            $parameters['tdigest'] = ['compression' => $this->compression];
        }

        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return new ArrayCollection(['percentiles' => $parameters]);
    }
}
