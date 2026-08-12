<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Index;
use Elasticsearch\Search\Builder;
use Elasticsearch\Search\PointInTime;
use Elasticsearch\Search\Queries\MatchAllQuery;
use Elasticsearch\Search\Queries\MatchPhraseQuery;
use Elasticsearch\Search\Queries\TermQuery;
use Elasticsearch\Search\Rescore\Enums\RescoreMode;
use Elasticsearch\Search\Rescore\Rescore;
use PHPUnit\Framework\TestCase;

class RequestBodyTest extends TestCase
{
    private function createBuilder(): Builder
    {
        return new Builder(new Index('product'));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Builder $builder): array
    {
        /** @var array<string, mixed> $payload */
        $payload = $builder->getPayload()->toArray();

        return $payload;
    }

    public function testPostFilter(): void
    {
        $builder = $this->createBuilder();
        $builder->setPostFilter(new TermQuery('inStock', true));

        $this->assertSame(['post_filter' => ['term' => ['inStock' => true]]], $this->payload($builder));
    }

    public function testMinScoreAndTracking(): void
    {
        $builder = $this->createBuilder();
        $builder->minScore(1.5)->trackTotalHits(true)->trackScores(true);

        $this->assertSame([
            'min_score'        => 1.5,
            'track_total_hits' => true,
            'track_scores'     => true,
        ], $this->payload($builder));
    }

    public function testTrackTotalHitsAcceptsLimit(): void
    {
        $builder = $this->createBuilder();
        $builder->trackTotalHits(10000);

        $this->assertSame(['track_total_hits' => 10000], $this->payload($builder));
    }

    public function testFalseValuesAreStillSent(): void
    {
        // false u track_* neni "nenastaveno" - musi se poslat
        $builder = $this->createBuilder();
        $builder->trackTotalHits(false)->trackScores(false);

        $this->assertSame([
            'track_total_hits' => false,
            'track_scores'     => false,
        ], $this->payload($builder));
    }

    public function testScriptFieldsAndRuntimeMappings(): void
    {
        $builder = $this->createBuilder();
        $builder->addScriptField('discounted', ['source' => "doc['price'].value * 0.8"])
            ->addRuntimeMapping('priceBand', [
                'type'   => 'keyword',
                'script' => ['source' => "emit(doc['price'].value > 100 ? 'high' : 'low')"],
            ]);

        $this->assertSame([
            'script_fields' => [
                'discounted' => ['script' => ['source' => "doc['price'].value * 0.8"]],
            ],
            'runtime_mappings' => [
                'priceBand' => [
                    'type'   => 'keyword',
                    'script' => ['source' => "emit(doc['price'].value > 100 ? 'high' : 'low')"],
                ],
            ],
        ], $this->payload($builder));
    }

    public function testSingleRescoreIsSentAsObject(): void
    {
        $builder = $this->createBuilder();
        $builder->addRescore(new Rescore(
            new MatchPhraseQuery('name', 'cerne boty'),
            window_size: 50,
            query_weight: 0.7,
            rescore_query_weight: 1.2,
            score_mode: RescoreMode::TOTAL
        ));

        $this->assertSame([
            'rescore' => [
                'window_size' => 50,
                'query'       => [
                    'rescore_query'        => ['match_phrase' => ['name' => ['query' => 'cerne boty']]],
                    'query_weight'         => 0.7,
                    'rescore_query_weight' => 1.2,
                    'score_mode'           => 'total',
                ],
            ],
        ], $this->payload($builder));
    }

    public function testMultipleRescoresAreSentAsList(): void
    {
        $builder = $this->createBuilder();
        $builder->addRescore(new Rescore(new MatchPhraseQuery('name', 'boty'), window_size: 100))
            ->addRescore(new Rescore(new MatchPhraseQuery('description', 'kozene'), window_size: 20));

        /** @var array{rescore: array<int, array<string, mixed>>} $payload */
        $payload = $this->payload($builder);

        $this->assertCount(2, $payload['rescore']);
        $this->assertSame(100, $payload['rescore'][0]['window_size']);
        $this->assertSame(20, $payload['rescore'][1]['window_size']);
    }

    public function testPointInTimeInBody(): void
    {
        $builder = $this->createBuilder();
        $builder->setPointInTime(new PointInTime('abc123', '2m'));

        $this->assertSame(
            ['pit' => ['id' => 'abc123', 'keep_alive' => '2m']],
            $this->payload($builder)
        );
    }

    public function testPointInTimeRemovesIndexFromRequest(): void
    {
        $builder = $this->createBuilder();
        $builder->setQuery(new MatchAllQuery());
        $builder->setIndexPrefix('testing_');

        $withoutPit = $builder->build()->toArray();
        $this->assertSame('testing_product', $withoutPit['index']);

        // s PIT patri index do pit.id; ES by request s indexem odmitl
        $builder->setPointInTime(new PointInTime('abc123'));
        $withPit = $builder->build()->toArray();
        $this->assertArrayNotHasKey('index', $withPit);
    }

    public function testEmptyBuilderProducesEmptyPayload(): void
    {
        $this->assertSame([], $this->payload($this->createBuilder()));
    }
}
