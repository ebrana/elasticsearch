<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithAggregations;
use Elasticsearch\Search\Queries\Query;

/**
 * Termy, ktere jsou ve vysledku dotazu vyrazne castejsi nez v celem indexu - hodi se na
 * "souvisejici hledani" nebo doporucene filtry. Na rozdil od terms nevraci nejcastejsi,
 * ale nejneobvyklejsi.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-significantterms-aggregation.html
 */
class SignificantTermsAggregation extends AbstractAggregation
{
    use WithAggregations;

    protected ?int $size = null;
    protected ?int $shardSize = null;
    protected ?int $minDocCount = null;
    protected ?Query $backgroundFilter = null;

    /** @var string[]|string|null */
    protected array|string|null $include = null;

    /** @var string[]|string|null */
    protected array|string|null $exclude = null;

    /** @var array<string, mixed>|null */
    protected ?array $heuristic = null;

    public function __construct(
        string $name,
        private readonly string $field,
        AbstractAggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function shardSize(int $shardSize): self
    {
        $this->shardSize = $shardSize;

        return $this;
    }

    public function minDocCount(int $minDocCount): self
    {
        $this->minDocCount = $minDocCount;

        return $this;
    }

    /**
     * Zuzi mnozinu, proti ktere se cetnost porovnava (vychozi je cely index).
     */
    public function backgroundFilter(Query $query): self
    {
        $this->backgroundFilter = $query;

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

    /**
     * Zpusob vypoctu vyznamnosti, napr. 'chi_square' nebo 'gnd'. Bez zadani pouzije ES jlh.
     *
     * @param array<string, mixed> $parameters
     */
    public function heuristic(string $type, array $parameters = []): self
    {
        $this->heuristic = [$type => [] === $parameters ? (object)[] : $parameters];

        return $this;
    }

    public function payload(): ArrayCollection
    {
        $parameters = ['field' => $this->field];

        if (null !== $this->size) {
            $parameters['size'] = $this->size;
        }
        if (null !== $this->shardSize) {
            $parameters['shard_size'] = $this->shardSize;
        }
        if (null !== $this->minDocCount) {
            $parameters['min_doc_count'] = $this->minDocCount;
        }
        if (null !== $this->include) {
            $parameters['include'] = $this->include;
        }
        if (null !== $this->exclude) {
            $parameters['exclude'] = $this->exclude;
        }
        if (null !== $this->backgroundFilter) {
            $parameters['background_filter'] = $this->backgroundFilter->toArray();
        }
        if (null !== $this->heuristic) {
            $parameters = array_merge($parameters, $this->heuristic);
        }

        $data = ['significant_terms' => $parameters];

        if (!$this->aggregations->isEmpty()) {
            $data['aggs'] = $this->aggregations->toArray();
        }

        return new ArrayCollection($data);
    }
}
