<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Aggregations\BucketScriptAggregation;
use Elasticsearch\Search\Aggregations\BucketSelectorAggregation;
use Elasticsearch\Search\Aggregations\BucketSortAggregation;
use Elasticsearch\Search\Aggregations\CumulativeSumAggregation;
use Elasticsearch\Search\Aggregations\DerivativeAggregation;
use Elasticsearch\Search\Aggregations\Enums\GapPolicy;
use PHPUnit\Framework\TestCase;

class PipelineAggregationsTest extends TestCase
{
    /**
     * @param array<string, mixed> $actual
     */
    private function assertJsonSame(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testBucketSelector(): void
    {
        $aggregation = new BucketSelectorAggregation(
            'jen_velke',
            ['celkem' => 'trzby'],
            'params.celkem > 200'
        );
        $aggregation->gapPolicy(GapPolicy::INSERT_ZEROS);

        $this->assertJsonSame(
            '{"bucket_selector":{"buckets_path":{"celkem":"trzby"},'
            . '"script":"params.celkem > 200","gap_policy":"insert_zeros"}}',
            $aggregation->toArray()
        );
    }

    public function testBucketScript(): void
    {
        $aggregation = new BucketScriptAggregation(
            'podil',
            ['cast' => 'trzby_znacky', 'celek' => 'trzby'],
            ['source' => 'params.cast / params.celek * 100']
        );
        $aggregation->format('0.00');

        $this->assertJsonSame(
            '{"bucket_script":{"buckets_path":{"cast":"trzby_znacky","celek":"trzby"},'
            . '"script":{"source":"params.cast / params.celek * 100"},"format":"0.00"}}',
            $aggregation->toArray()
        );
    }

    public function testBucketSortWithSortAndPaging(): void
    {
        $aggregation = new BucketSortAggregation('serazene');
        $aggregation->sort(['trzby' => ['order' => 'desc']])->from(1)->size(3);

        $this->assertJsonSame(
            '{"bucket_sort":{"sort":[{"trzby":{"order":"desc"}}],"from":1,"size":3}}',
            $aggregation->toArray()
        );
    }

    public function testBucketSortWithoutOptionsIsEmptyObject(): void
    {
        // bucket_sort jde pouzit i jen na strankovani; prazdne pole by ES odmitl
        $this->assertJsonSame('{"bucket_sort":{}}', (new BucketSortAggregation('a'))->toArray());
    }

    public function testDerivative(): void
    {
        $aggregation = new DerivativeAggregation('zmena', 'trzby');
        $aggregation->unit('day')->gapPolicy(GapPolicy::SKIP);

        $this->assertJsonSame(
            '{"derivative":{"buckets_path":"trzby","unit":"day","gap_policy":"skip"}}',
            $aggregation->toArray()
        );
    }

    public function testCumulativeSum(): void
    {
        $this->assertJsonSame(
            '{"cumulative_sum":{"buckets_path":"trzby"}}',
            (new CumulativeSumAggregation('narustajici', 'trzby'))->toArray()
        );
    }
}
