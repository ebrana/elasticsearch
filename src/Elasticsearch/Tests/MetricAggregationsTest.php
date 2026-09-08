<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Aggregations\AvgAggregation;
use Elasticsearch\Search\Aggregations\ExtendedStatsAggregation;
use Elasticsearch\Search\Aggregations\PercentileRanksAggregation;
use Elasticsearch\Search\Aggregations\PercentilesAggregation;
use Elasticsearch\Search\Aggregations\StatsAggregation;
use Elasticsearch\Search\Aggregations\TermsAggregation;
use Elasticsearch\Search\Aggregations\ValueCountAggregation;
use Elasticsearch\Search\Aggregations\WeightedAvgAggregation;
use PHPUnit\Framework\TestCase;

class MetricAggregationsTest extends TestCase
{
    public function testAvg(): void
    {
        $this->assertSame(['avg' => ['field' => 'price']], (new AvgAggregation('a', 'price'))->toArray());

        $aggregation = new AvgAggregation('a', 'price');
        $aggregation->missing('0');
        $this->assertSame(['avg' => ['field' => 'price', 'missing' => '0']], $aggregation->toArray());
    }

    public function testValueCount(): void
    {
        $this->assertSame(
            ['value_count' => ['field' => 'brand']],
            (new ValueCountAggregation('a', 'brand'))->toArray()
        );
    }

    public function testStats(): void
    {
        $this->assertSame(['stats' => ['field' => 'price']], (new StatsAggregation('a', 'price'))->toArray());
    }

    public function testExtendedStatsWithSigma(): void
    {
        $aggregation = new ExtendedStatsAggregation('a', 'price');
        $aggregation->sigma(3.0);

        $this->assertSame(
            ['extended_stats' => ['field' => 'price', 'sigma' => 3.0]],
            $aggregation->toArray()
        );
    }

    public function testPercentiles(): void
    {
        $aggregation = new PercentilesAggregation('a', 'price');
        $aggregation->percents([50.0, 95.0])->keyed(false)->compression(200.0);

        $this->assertSame([
            'percentiles' => [
                'field'    => 'price',
                'percents' => [50.0, 95.0],
                'keyed'    => false,
                'tdigest'  => ['compression' => 200.0],
            ],
        ], $aggregation->toArray());
    }

    public function testPercentileRanks(): void
    {
        $this->assertSame(
            ['percentile_ranks' => ['field' => 'price', 'values' => [100.0, 500.0]]],
            (new PercentileRanksAggregation('a', 'price', [100.0, 500.0]))->toArray()
        );
    }

    public function testWeightedAvg(): void
    {
        $aggregation = new WeightedAvgAggregation('a', 'rating', 'reviews');
        $aggregation->valueMissing(0.0)->weightMissing(1.0);

        $this->assertSame([
            'weighted_avg' => [
                'value'  => ['field' => 'rating', 'missing' => 0.0],
                'weight' => ['field' => 'reviews', 'missing' => 1.0],
            ],
        ], $aggregation->toArray());
    }

    public function testMetaIsAddedByAbstractAggregation(): void
    {
        $aggregation = new AvgAggregation('a', 'price');
        $aggregation->meta(['unit' => 'CZK']);

        $this->assertSame(
            ['avg' => ['field' => 'price'], 'meta' => ['unit' => 'CZK']],
            $aggregation->toArray()
        );
    }

    public function testTermsAggregationNewOptions(): void
    {
        $aggregation = new TermsAggregation('brands', 'brand');
        $aggregation->size(10)
            ->shardSize(100)
            ->minDocCount(2)
            ->include(['Alfa', 'Beta'])
            ->exclude('Gama.*');

        $this->assertSame([
            'terms' => [
                'field'         => 'brand',
                'size'          => 10,
                'shard_size'    => 100,
                'min_doc_count' => 2,
                'include'       => ['Alfa', 'Beta'],
                'exclude'       => 'Gama.*',
            ],
        ], $aggregation->toArray());
    }
}
