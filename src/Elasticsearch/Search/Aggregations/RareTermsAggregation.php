<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;

/**
 * The opposite of the terms aggregation - it returns the terms with the lowest document count.
 * Unlike `terms` with a reversed order it gives an exact result.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-rare-terms-aggregation.html
 */
class RareTermsAggregation extends AbstractAggregation
{
    use WithAggregations;

    protected ?int $maxDocCount = null;
    protected ?float $precision = null;

    /** @var string[]|string|null */
    protected array|string|null $include = null;

    /** @var string[]|string|null */
    protected array|string|null $exclude = null;

    public function __construct(
        string $name,
        private readonly string $field,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    /**
     * The upper document-count bound for a term to still count as rare (ES default 1).
     */
    public function maxDocCount(int $maxDocCount): self
    {
        $this->maxDocCount = $maxDocCount;

        return $this;
    }

    public function precision(float $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @param string[]|string $include
     */
    public function include(array|string $include): self
    {
        $this->include = $include;

        return $this;
    }

    /**
     * @param string[]|string $exclude
     */
    public function exclude(array|string $exclude): self
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['field' => $this->field];

        if (null !== $this->maxDocCount) {
            $parameters['max_doc_count'] = $this->maxDocCount;
        }
        if (null !== $this->precision) {
            $parameters['precision'] = $this->precision;
        }
        if (null !== $this->include) {
            $parameters['include'] = $this->include;
        }
        if (null !== $this->exclude) {
            $parameters['exclude'] = $this->exclude;
        }

        $data = ['rare_terms' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
