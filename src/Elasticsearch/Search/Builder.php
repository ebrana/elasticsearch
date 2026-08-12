<?php

declare(strict_types=1);

namespace Elasticsearch\Search;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Search\Aggregations\AbstractAggregation;
use Elasticsearch\Search\Aggregations\AggregationCollection;
use Elasticsearch\Search\Collapse\Collapse;
use Elasticsearch\Search\Highlight\Highlight;
use Elasticsearch\Search\Queries\Query;
use Elasticsearch\Search\Sorts\SortCollection;
use Elasticsearch\Search\Sorts\SortInterface;

final class Builder
{
    private ?Query $query = null;
    private ?AggregationCollection $aggregations = null;
    private ?SortCollection $sorts = null;
    private ?Collapse $collapse = null;
    private ?Highlight $highlight = null;
    private ?int $size = null;
    private ?int $from = null;
    private ?string $indexPrefix = null;

    /** @var null|array<string, string> */
    private ?array $searchAfter = null;

    /** @var null|array<string, string> */
    private ?array $fields = null;

    public function __construct(private readonly Index $index)
    {
    }

    public function setIndexPrefix(?string $indexPrefix): void
    {
        $this->indexPrefix = $indexPrefix;
    }

    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    public function setCollapse(?Collapse $collapse): void
    {
        $this->collapse = $collapse;
    }

    public function setHighlight(?Highlight $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function addAggregation(AbstractAggregation $aggregation): self
    {
        if (!$this->aggregations) {
            $this->aggregations = new AggregationCollection();
        }

        $this->aggregations->add($aggregation);

        return $this;
    }

    public function addSort(SortInterface $sort): self
    {
        if (!$this->sorts) {
            $this->sorts = new SortCollection();
        }

        $this->sorts->add($sort);

        return $this;
    }

    public function build(
        bool $withSort = true,
        bool $withAggregation = true
    ): ArrayCollection
    {
        $collection = new ArrayCollection();
        $body = $this->getPayload($withSort, $withAggregation)->toArray();
        if (!empty($body)) {
            $collection->set('body', $body);
        }
        $collection->set('index', $this->index->getName());
        if (null !== $this->indexPrefix) {
            $collection->set('index', $this->indexPrefix . $collection->get('index'));
        }

        return $collection;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function from(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param array<string,string>|null $searchAfter
     * @return $this
     */
    public function searchAfter(?array $searchAfter): self
    {
        $this->searchAfter = $searchAfter;

        return $this;
    }

    /**
     * @param string[] $fields
     * @return $this
     */
    public function fields(array $fields): self
    {
        $this->fields = array_merge($this->fields ?? [], $fields);

        return $this;
    }

    public function getPayload(
        bool $withSort = true,
        bool $withAggregation = true
    ): ArrayCollection
    {
        $collection = new ArrayCollection();

        if ($this->query) {
            $collection->set('query', $this->query->toArray());
        }

        if ($withAggregation && $this->aggregations) {
            $collection->set('aggs', $this->aggregations->toArray());
        }

        if ($withSort && $this->sorts) {
            $collection->set('sort', $this->sorts->toArray());
        }

        if ($this->fields) {
            $collection->set('_source', $this->fields);
        }

        if ($this->searchAfter) {
            $collection->set('search_after', $this->searchAfter);
        }

        if (null !== $this->size) {
            $collection->set('size', $this->size);
        }
        if (null !== $this->from) {
            $collection->set('from', $this->from);
        }

        if ($this->collapse) {
            $collection->set('collapse', $this->collapse->toArray());
        }

        if ($this->highlight) {
            $collection->set('highlight', $this->highlight->toArray());
        }

        return $collection;
    }
}
