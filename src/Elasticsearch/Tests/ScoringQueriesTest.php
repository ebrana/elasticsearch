<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Queries\Compound\BoostingQuery;
use Elasticsearch\Search\Queries\Compound\ConstantScoreQuery;
use Elasticsearch\Search\Queries\MatchAllQuery;
use Elasticsearch\Search\Queries\Specialized\DistanceFeatureQuery;
use Elasticsearch\Search\Queries\Specialized\MoreLikeThisQuery;
use Elasticsearch\Search\Queries\Specialized\PinnedQuery;
use Elasticsearch\Search\Queries\Specialized\RankFeature\LinearFunction;
use Elasticsearch\Search\Queries\Specialized\RankFeature\LogarithmFunction;
use Elasticsearch\Search\Queries\Specialized\RankFeature\SaturationFunction;
use Elasticsearch\Search\Queries\Specialized\RankFeature\SigmoidFunction;
use Elasticsearch\Search\Queries\Specialized\RankFeatureQuery;
use Elasticsearch\Search\Queries\Specialized\ScriptScoreQuery;
use Elasticsearch\Search\Queries\TermQuery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ScoringQueriesTest extends TestCase
{
    /**
     * Queries wrapping other queries are compared at the JSON level - MatchAllQuery deliberately
     * returns stdClass so that it serializes as {}, and that does not pass assertSame on an array.
     *
     * @param array<string, mixed> $actual
     */
    private function assertJsonQuery(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testConstantScoreQuery(): void
    {
        $this->assertJsonQuery(
            '{"constant_score":{"filter":{"term":{"inStock":true}},"boost":1.5}}',
            (new ConstantScoreQuery(new TermQuery('inStock', true), boost: 1.5))->toArray()
        );
    }

    public function testBoostingQuery(): void
    {
        $this->assertJsonQuery(
            '{"boosting":{"positive":{"match_all":{}},'
            . '"negative":{"term":{"sellingDenied":true}},"negative_boost":0.2}}',
            (new BoostingQuery(
                new MatchAllQuery(),
                new TermQuery('sellingDenied', true),
                negative_boost: 0.2
            ))->toArray()
        );
    }

    public function testScriptScoreQuery(): void
    {
        $this->assertJsonQuery(
            '{"script_score":{"query":{"match_all":{}},'
            . '"script":{"source":"doc[\'popularity\'].value * params.factor","params":{"factor":2}},'
            . '"min_score":1}}',
            (new ScriptScoreQuery(
                new MatchAllQuery(),
                ['source' => "doc['popularity'].value * params.factor", 'params' => ['factor' => 2]],
                min_score: 1.0
            ))->toArray()
        );
    }

    public function testDistanceFeatureQuery(): void
    {
        $this->assertSame(
            ['distance_feature' => ['field' => 'createdAt', 'origin' => 'now', 'pivot' => '7d']],
            (new DistanceFeatureQuery('createdAt', 'now', '7d'))->toArray()
        );

        $this->assertSame(
            ['distance_feature' => ['field' => 'location', 'origin' => [14.42, 50.08], 'pivot' => '10km']],
            (new DistanceFeatureQuery('location', [14.42, 50.08], '10km'))->toArray()
        );
    }

    public function testRankFeatureQueryFunctions(): void
    {
        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity"}}',
            (new RankFeatureQuery('popularity'))->toArray()
        );

        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity","saturation":{"pivot":80}}}',
            (new RankFeatureQuery('popularity', new SaturationFunction(80.0)))->toArray()
        );

        // saturation without a pivot lets ES compute it, but it must receive an empty object, not an array
        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity","saturation":{}}}',
            (new RankFeatureQuery('popularity', new SaturationFunction()))->toArray()
        );

        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity","log":{"scaling_factor":4}}}',
            (new RankFeatureQuery('popularity', new LogarithmFunction(4.0)))->toArray()
        );

        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity","sigmoid":{"pivot":7,"exponent":0.6}}}',
            (new RankFeatureQuery('popularity', new SigmoidFunction(7.0, 0.6)))->toArray()
        );

        $this->assertJsonQuery(
            '{"rank_feature":{"field":"popularity","linear":{}}}',
            (new RankFeatureQuery('popularity', new LinearFunction()))->toArray()
        );
    }

    public function testPinnedQueryWithIds(): void
    {
        $this->assertJsonQuery(
            '{"pinned":{"organic":{"match_all":{}},"ids":["1","2"]}}',
            (new PinnedQuery(new MatchAllQuery(), ids: ['1', '2']))->toArray()
        );
    }

    public function testPinnedQueryWithDocs(): void
    {
        $this->assertJsonQuery(
            '{"pinned":{"organic":{"match_all":{}},"docs":[{"_id":"1","_index":"product"}]}}',
            (new PinnedQuery(new MatchAllQuery(), docs: [['_id' => '1', '_index' => 'product']]))->toArray()
        );
    }

    public function testPinnedQueryRejectsBothIdsAndDocs(): void
    {
        $this->expectException(RuntimeException::class);

        (new PinnedQuery(new MatchAllQuery(), ids: ['1'], docs: [['_id' => '2']]))->toArray();
    }

    public function testPinnedQueryRequiresIdsOrDocs(): void
    {
        $this->expectException(RuntimeException::class);

        (new PinnedQuery(new MatchAllQuery()))->toArray();
    }

    public function testMoreLikeThisQuery(): void
    {
        $this->assertSame(
            ['more_like_this' => [
                'fields'          => ['name', 'description'],
                'like'            => [['_index' => 'product', '_id' => '1']],
                'min_term_freq'   => 1,
                'max_query_terms' => 12,
            ]],
            (new MoreLikeThisQuery(
                ['name', 'description'],
                [['_index' => 'product', '_id' => '1']],
                min_term_freq: 1,
                max_query_terms: 12
            ))->toArray()
        );
    }
}
