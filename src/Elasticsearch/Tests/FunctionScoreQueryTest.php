<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\BoostMode;
use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\FieldValueFactorModifier;
use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\MultiValueMode;
use Elasticsearch\Search\Queries\Compound\FunctionScore\Enums\ScoreMode;
use Elasticsearch\Search\Queries\Compound\FunctionScore\ExpDecayFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\FieldValueFactorFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\GaussDecayFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\LinearDecayFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\RandomScoreFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\ScriptScoreFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScore\WeightFunction;
use Elasticsearch\Search\Queries\Compound\FunctionScoreQuery;
use Elasticsearch\Search\Queries\MatchAllQuery;
use Elasticsearch\Search\Queries\TermQuery;
use PHPUnit\Framework\TestCase;

class FunctionScoreQueryTest extends TestCase
{
    /**
     * @param array<string, mixed> $actual
     */
    private function assertJsonQuery(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testWithoutFunctionsOmitsFunctionsKey(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}}}}',
            (new FunctionScoreQuery(new MatchAllQuery()))->toArray()
        );
    }

    public function testWeightFunctionWithFilter(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},'
            . '"functions":[{"filter":{"term":{"inStock":true}},"weight":3}],'
            . '"score_mode":"sum","boost_mode":"multiply"}}',
            (new FunctionScoreQuery(
                new MatchAllQuery(),
                [new WeightFunction(3.0, new TermQuery('inStock', true))],
                score_mode: ScoreMode::SUM,
                boost_mode: BoostMode::MULTIPLY
            ))->toArray()
        );
    }

    public function testFieldValueFactorFunction(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"field_value_factor":'
            . '{"field":"popularity","factor":1.2,"modifier":"sqrt","missing":1}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new FieldValueFactorFunction(
                    'popularity',
                    factor: 1.2,
                    modifier: FieldValueFactorModifier::SQRT,
                    missing: 1.0
                ),
            ]))->toArray()
        );
    }

    public function testRandomScoreFunction(): void
    {
        // with no parameters an empty object must go into the JSON, not an empty array
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"random_score":{}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [new RandomScoreFunction()]))->toArray()
        );

        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},'
            . '"functions":[{"random_score":{"seed":10,"field":"_seq_no"}}]}}',
            (new FunctionScoreQuery(
                new MatchAllQuery(),
                [new RandomScoreFunction(seed: 10, field: '_seq_no')]
            ))->toArray()
        );
    }

    public function testScriptScoreFunction(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"script_score":'
            . '{"script":{"source":"doc[\'popularity\'].value"}}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new ScriptScoreFunction(['source' => "doc['popularity'].value"]),
            ]))->toArray()
        );
    }

    public function testDecayFunctions(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"gauss":'
            . '{"createdAt":{"origin":"now","scale":"10d","offset":"5d","decay":0.5}}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new GaussDecayFunction('createdAt', 'now', '10d', offset: '5d', decay: 0.5),
            ]))->toArray()
        );

        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"exp":'
            . '{"price":{"origin":100,"scale":50}}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new ExpDecayFunction('price', 100, 50),
            ]))->toArray()
        );

        // multi_value_mode is a sibling of the field, not a part of it
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"linear":'
            . '{"price":{"origin":100,"scale":50},"multi_value_mode":"avg"}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new LinearDecayFunction('price', 100, 50, multi_value_mode: MultiValueMode::AVG),
            ]))->toArray()
        );
    }

    public function testDecayFunctionWithGeoOrigin(): void
    {
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},"functions":[{"gauss":'
            . '{"location":{"origin":{"lat":50.08,"lon":14.42},"scale":"10km"}}}]}}',
            (new FunctionScoreQuery(new MatchAllQuery(), [
                new GaussDecayFunction('location', ['lat' => 50.08, 'lon' => 14.42], '10km'),
            ]))->toArray()
        );
    }

    public function testAddFunctionAndFullOptions(): void
    {
        $query = new FunctionScoreQuery(
            new MatchAllQuery(),
            score_mode: ScoreMode::MAX,
            boost_mode: BoostMode::REPLACE,
            max_boost: 10.0,
            min_score: 1.0,
            boost: 2.0
        );
        $query->addFunction(new WeightFunction(2.0))
            ->addFunction(new FieldValueFactorFunction('popularity'));

        $this->assertCount(2, $query->getFunctions());
        $this->assertJsonQuery(
            '{"function_score":{"query":{"match_all":{}},'
            . '"functions":[{"weight":2},{"field_value_factor":{"field":"popularity"}}],'
            . '"score_mode":"max","boost_mode":"replace","max_boost":10,"min_score":1,"boost":2}}',
            $query->toArray()
        );
    }
}
