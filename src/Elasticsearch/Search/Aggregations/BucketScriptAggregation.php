<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Aggregations\Concerns\WithGapPolicy;

/**
 * Computes a new value from the metrics in the same bucket, e.g. the ratio of two sums.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-pipeline-bucket-script-aggregation.html
 */
class BucketScriptAggregation extends AbstractAggregation
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

        return new ArrayCollection(['bucket_script' => $parameters]);
    }
}
