<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Queries\Enums\Operator;
use Elasticsearch\Search\Queries\Enums\RegexpFlag;
use Elasticsearch\Search\Queries\Enums\SimpleQueryStringFlag;
use Elasticsearch\Search\Queries\FuzzyQuery;
use Elasticsearch\Search\Queries\IdsQuery;
use Elasticsearch\Search\Queries\MatchBoolPrefixQuery;
use Elasticsearch\Search\Queries\MatchPhrasePrefixQuery;
use Elasticsearch\Search\Queries\MatchPhraseQuery;
use Elasticsearch\Search\Queries\PrefixQuery;
use Elasticsearch\Search\Queries\RegexpQuery;
use Elasticsearch\Search\Queries\SimpleQueryStringQuery;
use Elasticsearch\Search\Queries\TermsSetQuery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FulltextQueriesTest extends TestCase
{
    public function testMatchPhraseQuery(): void
    {
        $this->assertSame(
            ['match_phrase' => ['name' => ['query' => 'cerne boty']]],
            (new MatchPhraseQuery('name', 'cerne boty'))->toArray()
        );

        $this->assertSame(
            ['match_phrase' => ['name' => [
                'query'            => 'cerne boty',
                'analyzer'         => 'czech_fulltext',
                'slop'             => 2,
                'zero_terms_query' => 'all',
                'boost'            => 2.0,
            ]]],
            (new MatchPhraseQuery(
                'name',
                'cerne boty',
                analyzer: 'czech_fulltext',
                slop: 2,
                zero_terms_query: 'all',
                boost: 2.0
            ))->toArray()
        );
    }

    public function testMatchPhrasePrefixQuery(): void
    {
        $this->assertSame(
            ['match_phrase_prefix' => ['name' => ['query' => 'cerne bo', 'max_expansions' => 10]]],
            (new MatchPhrasePrefixQuery('name', 'cerne bo', max_expansions: 10))->toArray()
        );
    }

    public function testMatchBoolPrefixQuery(): void
    {
        $this->assertSame(
            ['match_bool_prefix' => ['name' => [
                'query'                => 'cerne bo',
                'operator'             => 'AND',
                'minimum_should_match' => '2',
            ]]],
            (new MatchBoolPrefixQuery(
                'name',
                'cerne bo',
                operator: Operator::AND,
                minimum_should_match: '2'
            ))->toArray()
        );
    }

    public function testFuzzyQuery(): void
    {
        $this->assertSame(
            ['fuzzy' => ['code' => ['value' => 'ABC123', 'fuzziness' => 'AUTO', 'transpositions' => false]]],
            (new FuzzyQuery('code', 'ABC123', fuzziness: 'AUTO', transpositions: false))->toArray()
        );
    }

    public function testRegexpQueryJoinsFlags(): void
    {
        $this->assertSame(
            ['regexp' => ['code' => [
                'value'            => 'AB.*',
                'flags'            => 'COMPLEMENT|INTERVAL',
                'case_insensitive' => true,
            ]]],
            (new RegexpQuery(
                'code',
                'AB.*',
                flags: [RegexpFlag::COMPLEMENT, RegexpFlag::INTERVAL],
                case_insensitive: true
            ))->toArray()
        );
    }

    public function testIdsQuery(): void
    {
        $this->assertSame(
            ['ids' => ['values' => ['1', '2']]],
            (new IdsQuery(['1', '2']))->toArray()
        );
    }

    public function testTermsSetQuery(): void
    {
        $this->assertSame(
            ['terms_set' => ['tags' => [
                'terms'                      => ['akce', 'novinka'],
                'minimum_should_match_field' => 'required_matches',
            ]]],
            (new TermsSetQuery('tags', ['akce', 'novinka'], minimum_should_match_field: 'required_matches'))
                ->toArray()
        );
    }

    public function testTermsSetQueryRequiresMinimumShouldMatch(): void
    {
        $this->expectException(RuntimeException::class);

        (new TermsSetQuery('tags', ['akce']))->toArray();
    }

    public function testPrefixQueryKeepsOptionsInsideField(): void
    {
        // rewrite a case_insensitive musi byt uvnitr objektu pole; vedle nej je ES odmita
        // hlaskou "query doesn't support multiple fields"
        $this->assertSame(
            ['prefix' => ['code' => [
                'value'            => 'ab',
                'rewrite'          => 'constant_score',
                'case_insensitive' => true,
            ]]],
            (new PrefixQuery('code', 'ab', rewrite: 'constant_score', case_insensitive: true))->toArray()
        );
    }

    public function testSimpleQueryStringQuery(): void
    {
        $this->assertSame(
            ['simple_query_string' => [
                'query'            => 'cerne + boty',
                'fields'           => ['name^3', 'description'],
                'flags'            => 'AND|OR|PREFIX',
                'default_operator' => 'AND',
                'lenient'          => true,
            ]],
            (new SimpleQueryStringQuery(
                'cerne + boty',
                fields: ['name^3', 'description'],
                flags: [SimpleQueryStringFlag::AND, SimpleQueryStringFlag::OR, SimpleQueryStringFlag::PREFIX],
                default_operator: Operator::AND,
                lenient: true
            ))->toArray()
        );
    }
}
