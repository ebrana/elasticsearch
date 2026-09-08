<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithGapPolicy;

/**
 * Sorts or pages already computed buckets. Without `sort` it works as paging only.
 * Beware: the parent aggregation must already use a large enough `size`, otherwise the sorting
 * only covers what survived its cut.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-bucket-sort-aggregation.html
 */
class BucketSortAggregation extends AbstractAggregation
{
    use WithGapPolicy;

    /** @var array<int, mixed> */
    protected array $sort = [];

    protected ?int $from = null;
    protected ?int $size = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * E.g. ['total_sales' => ['order' => 'desc']] or just 'total_sales'.
     *
     * @param array<string, mixed>|string $sort
     */
    public function sort(array|string $sort): self
    {
        $this->sort[] = $sort;

        return $this;
    }

    public function from(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [];

        if ([] !== $this->sort) {
            $parameters['sort'] = $this->sort;
        }
        if (null !== $this->from) {
            $parameters['from'] = $this->from;
        }
        if (null !== $this->size) {
            $parameters['size'] = $this->size;
        }
        $this->provideGapPolicy($parameters);

        // with no parameters an empty object must go into the JSON, not an empty array
        return new ArrayCollection(['bucket_sort' => [] === $parameters ? (object)[] : $parameters]);
    }
}
