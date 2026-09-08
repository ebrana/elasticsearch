<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Aggregations\Concerns\WithMissing;
use RuntimeException;

/**
 * Rozdeli datumy do casovych intervalu. Zada se bud `calendar_interval` (kalendarni jednotka
 * jako month nebo week, respektuje delku mesice a letni cas), nebo `fixed_interval`
 * (pevny usek jako "30d") - prave jedno z nich.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-datehistogram-aggregation.html
 */
class DateHistogramAggregation extends AbstractAggregation
{
    use WithAggregations;
    use WithMissing;

    protected ?string $calendarInterval = null;
    protected ?string $fixedInterval = null;
    protected ?string $format = null;
    protected ?string $timeZone = null;
    protected ?string $offset = null;
    protected ?int $minDocCount = null;
    protected ?bool $keyed = null;

    /** @var array<string, string>|null */
    protected ?array $extendedBounds = null;

    /** @var array<string, string>|null */
    protected ?array $order = null;

    public function __construct(
        string $name,
        private readonly string $field,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    /**
     * Kalendarni jednotka: minute, hour, day, week, month, quarter, year.
     */
    public function calendarInterval(string $interval): self
    {
        $this->calendarInterval = $interval;

        return $this;
    }

    /**
     * A fixed interval, e.g. "30d" or "12h".
     */
    public function fixedInterval(string $interval): self
    {
        $this->fixedInterval = $interval;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function timeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function offset(string $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function minDocCount(int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    public function keyed(bool $keyed): self
    {
        $this->keyed = $keyed;

        return $this;
    }

    public function extendedBounds(string $min, string $max): self
    {
        $this->extendedBounds = ['min' => $min, 'max' => $max];

        return $this;
    }

    /**
     * @param array<string, string> $order
     */
    public function order(array $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        if (null === $this->calendarInterval && null === $this->fixedInterval) {
            throw new RuntimeException(
                'Date histogram aggregation must define calendarInterval or fixedInterval.'
            );
        }

        if (null !== $this->calendarInterval && null !== $this->fixedInterval) {
            throw new RuntimeException(
                'Date histogram aggregation accepts either calendarInterval or fixedInterval, not both.'
            );
        }

        $parameters = ['field' => $this->field];

        if (null !== $this->calendarInterval) {
            $parameters['calendar_interval'] = $this->calendarInterval;
        }
        if (null !== $this->fixedInterval) {
            $parameters['fixed_interval'] = $this->fixedInterval;
        }
        if (null !== $this->format) {
            $parameters['format'] = $this->format;
        }
        if (null !== $this->timeZone) {
            $parameters['time_zone'] = $this->timeZone;
        }
        if (null !== $this->offset) {
            $parameters['offset'] = $this->offset;
        }
        if (null !== $this->minDocCount) {
            $parameters['min_doc_count'] = $this->minDocCount;
        }
        if (null !== $this->keyed) {
            $parameters['keyed'] = $this->keyed;
        }
        if (null !== $this->extendedBounds) {
            $parameters['extended_bounds'] = $this->extendedBounds;
        }
        if (null !== $this->order) {
            $parameters['order'] = $this->order;
        }
        if (null !== $this->missing) {
            $parameters['missing'] = $this->missing;
        }

        $data = ['date_histogram' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
