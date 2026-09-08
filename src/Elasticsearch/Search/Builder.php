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
use Elasticsearch\Search\Rescore\Rescore;
use Elasticsearch\Search\Sorts\SortCollection;
use Elasticsearch\Search\Sorts\SortInterface;
use Elasticsearch\Search\Suggest\Suggest;

final class Builder
{
    private ?Query $query = null;
    private ?AggregationCollection $aggregations = null;
    private ?SortCollection $sorts = null;
    private ?Collapse $collapse = null;
    private ?Highlight $highlight = null;
    private ?Suggest $suggest = null;
    private ?Query $postFilter = null;
    private ?PointInTime $pointInTime = null;
    private ?float $minScore = null;
    private bool|int|null $trackTotalHits = null;
    private ?bool $trackScores = null;

    /** @var Rescore[] */
    private array $rescores = [];

    /** @var array<string, array<string, mixed>> */
    private array $scriptFields = [];

    /** @var array<string, array<string, mixed>> */
    private array $runtimeMappings = [];
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

    public function setSuggest(?Suggest $suggest): void
    {
        $this->suggest = $suggest;
    }

    /**
     * A filter applied only after the aggregations have been computed - the aggregations therefore
     * count from the result before this filter. Typical for facets, where the selected value
     * must not narrow the offer.
     */
    public function setPostFilter(?Query $postFilter): void
    {
        $this->postFilter = $postFilter;
    }

    /**
     * With a Point in Time the index is not sent in the request but is part of the PIT -
     * build() therefore omits it.
     */
    public function setPointInTime(?PointInTime $pointInTime): void
    {
        $this->pointInTime = $pointInTime;
    }

    public function minScore(?float $minScore): self
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * true = an exact count, a number = exact up to the given limit (above it the relation is "gte").
     */
    public function trackTotalHits(bool|int|null $trackTotalHits): self
    {
        $this->trackTotalHits = $trackTotalHits;

        return $this;
    }

    /**
     * Returns the score even when sorting by a field - otherwise _score is null.
     */
    public function trackScores(?bool $trackScores): self
    {
        $this->trackScores = $trackScores;

        return $this;
    }

    public function addRescore(Rescore $rescore): self
    {
        $this->rescores[] = $rescore;

        return $this;
    }

    /**
     * @param array<string, mixed> $script the script the way ES expects it (including the source key)
     */
    public function addScriptField(string $name, array $script): self
    {
        $this->scriptFields[$name] = ['script' => $script];

        return $this;
    }

    /**
     * A field computed at search time, without being written into the index.
     *
     * @param array<string, mixed> $definition e.g. ['type' => 'keyword', 'script' => [...]]
     */
    public function addRuntimeMapping(string $field, array $definition): self
    {
        $this->runtimeMappings[$field] = $definition;

        return $this;
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
        // with a PIT the index belongs into pit.id, not into the request; ES would reject it otherwise
        if (null === $this->pointInTime) {
            $collection->set('index', $this->index->getName());
            if (null !== $this->indexPrefix) {
                $collection->set('index', $this->indexPrefix . $collection->get('index'));
            }
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

        if ($this->suggest) {
            $collection->set('suggest', $this->suggest->toArray());
        }

        if ($this->postFilter) {
            $collection->set('post_filter', $this->postFilter->toArray());
        }

        if (null !== $this->minScore) {
            $collection->set('min_score', $this->minScore);
        }

        if (null !== $this->trackTotalHits) {
            $collection->set('track_total_hits', $this->trackTotalHits);
        }

        if (null !== $this->trackScores) {
            $collection->set('track_scores', $this->trackScores);
        }

        if ($this->scriptFields) {
            $collection->set('script_fields', $this->scriptFields);
        }

        if ($this->runtimeMappings) {
            $collection->set('runtime_mappings', $this->runtimeMappings);
        }

        if ($this->rescores) {
            $rescores = array_map(static fn (Rescore $rescore): array => $rescore->toArray(), $this->rescores);
            // a single rescore can be sent as an object, several of them must be an array
            $collection->set('rescore', 1 === count($rescores) ? $rescores[0] : $rescores);
        }

        if ($this->pointInTime) {
            $collection->set('pit', $this->pointInTime->toArray());
        }

        return $collection;
    }
}
