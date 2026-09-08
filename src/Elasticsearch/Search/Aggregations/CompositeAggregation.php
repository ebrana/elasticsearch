<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Composite\CompositeSourceInterface;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use RuntimeException;

/**
 * A pageable aggregation over combinations of values. It is the only one able to walk through
 * absolutely all buckets - the response returns `after_key`, which is passed to `after()`
 * to get the next page.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-composite-aggregation.html
 */
class CompositeAggregation extends AbstractAggregation
{
    use WithAggregations;

    /** @var CompositeSourceInterface[] */
    protected array $sources = [];

    protected ?int $size = null;

    /** @var array<string, mixed>|null */
    protected ?array $after = null;

    public function __construct(
        string $name,
        CompositeSourceInterface ...$sources
    ) {
        $this->name = $name;
        $this->sources = $sources;
        $this->aggregations = new AggregationCollection();
    }

    public function source(CompositeSourceInterface $source): self
    {
        $this->sources[] = $source;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * `after_key` z predchozi odpovedi.
     *
     * @param array<string, mixed> $after
     */
    public function after(array $after): self
    {
        $this->after = $after;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        if ([] === $this->sources) {
            throw new RuntimeException('Composite aggregation must define at least one source.');
        }

        $parameters = [
            'sources' => array_map(
                static fn (CompositeSourceInterface $source): array => $source->toArray(),
                $this->sources
            ),
        ];

        if (null !== $this->size) {
            $parameters['size'] = $this->size;
        }

        if (null !== $this->after) {
            $parameters['after'] = $this->after;
        }

        $data = ['composite' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
