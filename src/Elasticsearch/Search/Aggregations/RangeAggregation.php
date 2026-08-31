<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;
use RuntimeException;

/**
 * Rozdeli hodnoty do zadanych intervalu. Na rozdil od histogramu nemusi byt stejne siroke.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-range-aggregation.html
 */
class RangeAggregation extends AbstractAggregation
{
    use WithAggregations;
    use WithMissing;

    /** @var Range[] */
    protected array $ranges = [];

    protected ?bool $keyed = null;

    public function __construct(
        string $name,
        private readonly string $field,
        Range ...$ranges
    ) {
        $this->name = $name;
        $this->ranges = $ranges;
        $this->aggregations = new AggregationCollection();
    }

    public function range(Range $range): self
    {
        $this->ranges[] = $range;

        return $this;
    }

    public function keyed(bool $keyed): self
    {
        $this->keyed = $keyed;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        if ([] === $this->ranges) {
            throw new RuntimeException('RangeAggregation must define at least one range.');
        }

        $parameters = [
            'field'  => $this->field,
            'ranges' => array_map(static fn (Range $range): array => $range->toArray(), $this->ranges),
        ];

        if (null !== $this->keyed) {
            $parameters['keyed'] = $this->keyed;
        }
        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        $data = ['range' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
