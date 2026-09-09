<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithGapPolicy;

/**
 * Drops the buckets for which the script returns false - the equivalent of HAVING in SQL. It runs
 * over already computed buckets, so the parent aggregation's `size` is applied before it.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-bucket-selector-aggregation.html
 */
class BucketSelectorAggregation extends AbstractAggregation
{
    use WithGapPolicy;

    /**
     * @param array<string, string> $bucketsPath script variable name => path to the metric
     * @param array<string, mixed>|string $script
     */
    public function __construct(
        string $name,
        private readonly array $bucketsPath,
        private readonly array|string $script,
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'buckets_path' => $this->bucketsPath,
            'script'       => $this->script,
        ];
        $this->provideGapPolicy($parameters);

        return new ArrayCollection(['bucket_selector' => $parameters]);
    }
}
