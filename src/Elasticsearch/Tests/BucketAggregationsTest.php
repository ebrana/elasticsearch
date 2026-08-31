<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Aggregations\AvgAggregation;
use Elasticsearch\Search\Aggregations\Composite\DateHistogramSource;
use Elasticsearch\Search\Aggregations\Composite\HistogramSource;
use Elasticsearch\Search\Aggregations\Composite\TermsSource;
use Elasticsearch\Search\Aggregations\CompositeAggregation;
use Elasticsearch\Search\Aggregations\GlobalAggregation;
use Elasticsearch\Search\Aggregations\DateHistogramAggregation;
use Elasticsearch\Search\Aggregations\DateRangeAggregation;
use Elasticsearch\Search\Aggregations\FiltersAggregation;
use Elasticsearch\Search\Aggregations\HistogramAggregation;
use Elasticsearch\Search\Aggregations\MissingAggregation;
use Elasticsearch\Search\Aggregations\MultiTermsAggregation;
use Elasticsearch\Search\Aggregations\Range;
use Elasticsearch\Search\Aggregations\RangeAggregation;
use Elasticsearch\Search\Aggregations\RareTermsAggregation;
use Elasticsearch\Search\Aggregations\SamplerAggregation;
use Elasticsearch\Search\Aggregations\SignificantTermsAggregation;
use Elasticsearch\Search\Queries\TermQuery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BucketAggregationsTest extends TestCase
{
    /**
     * @param array<string, mixed> $actual
     */
    private function assertJsonSame(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testHistogram(): void
    {
        $aggregation = new HistogramAggregation('cenova_pasma', 'price', 100.0);
        $aggregation->minDocCount(0)->extendedBounds(0.0, 1000.0);

        $this->assertJsonSame(
            '{"histogram":{"field":"price","interval":100,"min_doc_count":0,'
            . '"extended_bounds":{"min":0,"max":1000}}}',
            $aggregation->toArray()
        );
    }

    public function testDateHistogramWithCalendarInterval(): void
    {
        $aggregation = new DateHistogramAggregation('po_mesicich', 'createdAt');
        $aggregation->calendarInterval('month')->format('yyyy-MM')->timeZone('Europe/Prague');

        $this->assertJsonSame(
            '{"date_histogram":{"field":"createdAt","calendar_interval":"month",'
            . '"format":"yyyy-MM","time_zone":"Europe/Prague"}}',
            $aggregation->toArray()
        );
    }

    public function testDateHistogramRequiresInterval(): void
    {
        $this->expectException(RuntimeException::class);

        (new DateHistogramAggregation('a', 'createdAt'))->toArray();
    }

    public function testDateHistogramRejectsBothIntervals(): void
    {
        $aggregation = new DateHistogramAggregation('a', 'createdAt');
        $aggregation->calendarInterval('month')->fixedInterval('30d');

        $this->expectException(RuntimeException::class);

        $aggregation->toArray();
    }

    public function testRange(): void
    {
        $aggregation = new RangeAggregation(
            'pasma',
            'price',
            new Range(to: 100, key: 'levne'),
            new Range(from: 100, to: 500),
            new Range(from: 500, key: 'drahe')
        );

        $this->assertJsonSame(
            '{"range":{"field":"price","ranges":[{"key":"levne","to":100},'
            . '{"from":100,"to":500},{"key":"drahe","from":500}]}}',
            $aggregation->toArray()
        );
    }

    public function testRangeRequiresAtLeastOneRange(): void
    {
        $this->expectException(RuntimeException::class);

        (new RangeAggregation('a', 'price'))->toArray();
    }

    public function testDateRange(): void
    {
        $aggregation = new DateRangeAggregation('obdobi', 'createdAt', new Range(from: 'now-1M/M', to: 'now'));
        $aggregation->format('yyyy-MM-dd');

        $this->assertJsonSame(
            '{"date_range":{"field":"createdAt","ranges":[{"from":"now-1M/M","to":"now"}],'
            . '"format":"yyyy-MM-dd"}}',
            $aggregation->toArray()
        );
    }

    public function testFilters(): void
    {
        $aggregation = new FiltersAggregation('stavy', [
            'skladem'    => new TermQuery('inStock', true),
            'neskladem'  => new TermQuery('inStock', false),
        ]);
        $aggregation->otherBucket(true, 'ostatni');

        $this->assertJsonSame(
            '{"filters":{"filters":{"skladem":{"term":{"inStock":true}},'
            . '"neskladem":{"term":{"inStock":false}}},"other_bucket":true,'
            . '"other_bucket_key":"ostatni"}}',
            $aggregation->toArray()
        );
    }

    public function testFiltersRequiresAtLeastOneFilter(): void
    {
        $this->expectException(RuntimeException::class);

        (new FiltersAggregation('a'))->toArray();
    }

    public function testMissingWithSubAggregation(): void
    {
        $aggregation = new MissingAggregation('bez_ceny', 'price', new AvgAggregation('prumer', 'rating'));

        $this->assertJsonSame(
            '{"missing":{"field":"price"},"aggs":{"prumer":{"avg":{"field":"rating"}}}}',
            $aggregation->toArray()
        );
    }

    public function testSamplerWithoutOptionsIsEmptyObject(): void
    {
        $this->assertJsonSame('{"sampler":{}}', (new SamplerAggregation('vzorek'))->toArray());

        $aggregation = new SamplerAggregation('vzorek');
        $aggregation->shardSize(200);
        $this->assertJsonSame('{"sampler":{"shard_size":200}}', $aggregation->toArray());
    }

    public function testMultiTerms(): void
    {
        $aggregation = new MultiTermsAggregation('kombinace', 'brand', 'color');
        $aggregation->size(10)->minDocCount(2);

        $this->assertJsonSame(
            '{"multi_terms":{"terms":[{"field":"brand"},{"field":"color"}],"size":10,"min_doc_count":2}}',
            $aggregation->toArray()
        );
    }

    public function testMultiTermsRequiresTwoFields(): void
    {
        $this->expectException(RuntimeException::class);

        (new MultiTermsAggregation('a', 'brand'))->toArray();
    }

    public function testRareTerms(): void
    {
        $aggregation = new RareTermsAggregation('vzacne', 'brand');
        $aggregation->maxDocCount(3)->exclude('Alfa');

        $this->assertJsonSame(
            '{"rare_terms":{"field":"brand","max_doc_count":3,"exclude":"Alfa"}}',
            $aggregation->toArray()
        );
    }

    public function testSignificantTerms(): void
    {
        $aggregation = new SignificantTermsAggregation('vyznamne', 'brand');
        $aggregation->size(5)
            ->backgroundFilter(new TermQuery('active', true))
            ->heuristic('chi_square', ['background_is_superset' => false]);

        $this->assertJsonSame(
            '{"significant_terms":{"field":"brand","size":5,'
            . '"background_filter":{"term":{"active":true}},'
            . '"chi_square":{"background_is_superset":false}}}',
            $aggregation->toArray()
        );
    }

    public function testSignificantTermsHeuristicWithoutParametersIsEmptyObject(): void
    {
        $aggregation = new SignificantTermsAggregation('vyznamne', 'brand');
        $aggregation->heuristic('gnd');

        $this->assertJsonSame('{"significant_terms":{"field":"brand","gnd":{}}}', $aggregation->toArray());
    }

    public function testComposite(): void
    {
        $terms = new TermsSource('znacka', 'brand');
        $terms->order('desc')->missingBucket(true);

        $aggregation = new CompositeAggregation(
            'strankovane',
            $terms,
            new HistogramSource('cena', 'price', 100.0),
            new DateHistogramSource('mesic', 'createdAt', 'month')
        );
        $aggregation->size(100)->after(['znacka' => 'Alfa', 'cena' => 100.0, 'mesic' => 1234567890]);

        $this->assertJsonSame(
            '{"composite":{"sources":['
            . '{"znacka":{"terms":{"field":"brand","order":"desc","missing_bucket":true}}},'
            . '{"cena":{"histogram":{"field":"price","interval":100}}},'
            . '{"mesic":{"date_histogram":{"field":"createdAt","calendar_interval":"month"}}}'
            . '],"size":100,"after":{"znacka":"Alfa","cena":100,"mesic":1234567890}}}',
            $aggregation->toArray()
        );
    }

    public function testCompositeRequiresSource(): void
    {
        $this->expectException(RuntimeException::class);

        (new CompositeAggregation('a'))->toArray();
    }

    public function testCompositeDateHistogramSourceWithFixedInterval(): void
    {
        $source = new DateHistogramSource('den', 'createdAt', '30d', calendar: false);

        $this->assertJsonSame(
            '{"composite":{"sources":[{"den":{"date_histogram":{"field":"createdAt","fixed_interval":"30d"}}}]}}',
            (new CompositeAggregation('a', $source))->toArray()
        );
    }

    public function testGlobalAggregationOmitsEmptyAggs(): void
    {
        // prazdne "aggs" ES odmita hlaskou "Expected [START_OBJECT] under [aggs]"
        $this->assertJsonSame('{"global":{}}', (new GlobalAggregation('vse'))->toArray());

        $aggregation = new GlobalAggregation('vse');
        $aggregation->aggregation(new AvgAggregation('prumer', 'price'));

        $this->assertJsonSame(
            '{"global":{},"aggs":{"prumer":{"avg":{"field":"price"}}}}',
            $aggregation->toArray()
        );
    }
}
