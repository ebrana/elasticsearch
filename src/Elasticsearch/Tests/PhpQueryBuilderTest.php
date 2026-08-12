<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Tools\PhpQueryBuilder;
use PHPUnit\Framework\TestCase;

class PhpQueryBuilderTest extends TestCase
{
    public function testFromJsonTermQuery(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{
          "query" : {
            "term" : { "user.id" : "kimchy" }
          }
        }');

        $this->assertSame("\$termQuery = new TermQuery(field: 'user.id', value: 'kimchy');", $result);
    }

    public function testFromArrayWithQueryKey(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromArray([
            'query' => [
                'match_all' => [],
            ],
        ]);

        $this->assertSame("\$matchAllQuery = new MatchAllQuery();", $result);
    }

    public function testBoolQueryWithClauses(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{
          "query": {
            "bool": {
              "must": [
                { "term": { "user.id": "kimchy" } },
                { "exists": { "field": "email" } }
              ],
              "filter": { "range": { "price": { "gte": 10, "lt": 30 } } },
              "minimum_should_match": "2<-25%",
              "boost": 1.5
            }
          }
        }');

        $this->assertStringContainsString("\$boolQuery = new BoolQuery();", $result);
        $this->assertStringContainsString("\$must0 = new TermQuery(field: 'user.id', value: 'kimchy');", $result);
        $this->assertStringContainsString("\$boolQuery->add(\$must0, BoolType::MUST);", $result);
        $this->assertStringContainsString("\$must1 = new ExistsQuery('email');", $result);
        $this->assertStringContainsString("\$boolQuery->add(\$must1, BoolType::MUST);", $result);
        $this->assertStringContainsString("\$filter = new RangeQuery(field: 'price');", $result);
        $this->assertStringContainsString("\$filter->gte(10);", $result);
        $this->assertStringContainsString("\$filter->lt(30);", $result);
        $this->assertStringContainsString("\$boolQuery->setMinimumShouldMatch('2<-25%');", $result);
        $this->assertStringContainsString("\$boolQuery->setBoost(1.5);", $result);
    }

    public function testFromArrayWithoutRootQueryKey(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromArray([
            'exists' => [
                'field' => 'status',
            ],
        ]);

        $this->assertSame("\$existsQuery = new ExistsQuery('status');", $result);
    }

    public function testDisMaxAndQueryStringResolvers(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromArray([
            'query' => [
                'dis_max' => [
                    'tie_breaker' => 0.7,
                    'queries' => [
                        ['query_string' => [
                            'query' => 'status:active',
                            'default_operator' => 'AND',
                            'fields' => ['status', 'category'],
                            'lenient' => true,
                        ]],
                        ['match_all' => []],
                    ],
                ],
            ],
        ]);

        $this->assertStringContainsString("\$disMaxQuery = new DisMaxQuery(queries: [], tie_breaker: 0.7);", $result);
        $this->assertStringContainsString("\$disMaxQuery0 = new QueryStringQuery('status:active');", $result);
        $this->assertStringContainsString("\$disMaxQuery0->setDefaultOperator(Operator::AND);", $result);
        $this->assertStringContainsString("\$disMaxQuery0->field('status');", $result);
        $this->assertStringContainsString("\$disMaxQuery0->field('category');", $result);
        $this->assertStringContainsString("\$disMaxQuery0->setLenient(true);", $result);
        $this->assertStringContainsString("\$disMaxQuery1 = new MatchAllQuery();", $result);
        $this->assertStringContainsString("\$disMaxQuery->addQuery(\$disMaxQuery0);", $result);
        $this->assertStringContainsString("\$disMaxQuery->addQuery(\$disMaxQuery1);", $result);
    }

    public function testBodyWithSortCollapseAndPagination(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromArray([
            'body' => [
                'query' => [
                    'term' => [
                        'show.cs_CZ' => true,
                    ],
                ],
                'sort' => [
                    ['tags.priority' => ['order' => 'desc', 'missing' => '_last']],
                ],
                'collapse' => [
                    'field' => 'categories',
                    'inner_hits' => [
                        'name' => 'innerHits',
                        'size' => 10,
                        'collapse' => ['field' => 'user.id'],
                        'sort' => [
                            ['tags.priority' => ['order' => 'desc']],
                        ],
                    ],
                ],
                'size' => 0,
                'from' => 25,
                '_source' => [
                    'includes' => ['id', 'name'],
                ],
            ],
        ]);

        $this->assertStringContainsString("\$query = new TermQuery(field: 'show.cs_CZ', value: true);", $result);
        $this->assertStringContainsString('$builder->setQuery($query);', $result);
        $this->assertStringContainsString("\$sort1 = new Sort('tags.priority', SortDirection::DESC);", $result);
        $this->assertStringContainsString("\$sort1->missing('_last');", $result);
        $this->assertStringContainsString('$builder->addSort($sort1);', $result);
        $this->assertStringContainsString("\$innerHitsCollection1 = new InnerHitsCollection();", $result);
        $this->assertStringContainsString("\$innerHits1_0 = new InnerHits(name: 'innerHits', size: 10, collapseField: 'user.id', sort: ['tags.priority' => 'desc']);", $result);
        $this->assertStringContainsString("\$collapse1 = new Collapse(field: 'categories', innerHits: \$innerHitsCollection1);", $result);
        $this->assertStringContainsString('$builder->setCollapse($collapse1);', $result);
        $this->assertStringContainsString('$builder->size(0);', $result);
        $this->assertStringContainsString('$builder->from(25);', $result);
        $this->assertStringContainsString("\$builder->fields(['id', 'name']);", $result);
    }

    public function testComplexBodyPayloadFromJson(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{"body":{"query":{"bool":{"must":[{"nested":{"path":"categories","query":{"term":{"categories.id":"019c1de6-872e-700c-becb-12b4790631ea"}}}},{"term":{"show.cs_CZ":true}}]}},"aggs":{"technical_params_optimized":{"filter":{"bool":{"must":[{"term":{"show.cs_CZ":true}},{"nested":{"path":"categories","query":{"term":{"categories.id":"019c1de6-872e-700c-becb-12b4790631ea"}}}}]}},"aggs":{"nested_params_split":{"nested":{"path":"technicalParameters"},"aggs":{"group_whitelist":{"filter":{"terms":{"technicalParameters.alias":["collection","type","color"]}},"aggs":{"by_param":{"terms":{"field":"technicalParameters.alias","size":1000,"order":{"_key":"asc"}},"aggs":{"type":{"top_hits":{"size":1,"_source":{"includes":["technicalParameters.type","technicalParameters.name","technicalParameters.alias","technicalParameters.position","technicalParameters.alphabeticalOrder","technicalParameters.units.cs_CZ"]}}},"values_nested":{"nested":{"path":"technicalParameters.values"},"aggs":{"by_value_alias":{"terms":{"field":"technicalParameters.values.alias","size":1000,"order":{"_key":"asc"}},"aggs":{"value_name":{"top_hits":{"size":1,"_source":{"includes":["technicalParameters.values.name.cs_CZ","technicalParameters.values.position"]}}},"total_count":{"cardinality":{"field":"id","precision_threshold":30000}}}}}},"min_number":{"min":{"field":"technicalParameters.number"}},"max_number":{"max":{"field":"technicalParameters.number"}}}}}},"group_others":{"filter":{"bool":{"must_not":[{"terms":{"technicalParameters.alias":["collection","type","color"]}}]}},"aggs":{"by_param":{"terms":{"field":"technicalParameters.alias","size":1000,"order":{"_key":"asc"}},"aggs":{"type":{"top_hits":{"size":1,"_source":{"includes":["technicalParameters.type","technicalParameters.name","technicalParameters.alias","technicalParameters.position","technicalParameters.alphabeticalOrder","technicalParameters.units.cs_CZ"]}}},"values_nested":{"nested":{"path":"technicalParameters.values"},"aggs":{"by_value_alias":{"terms":{"field":"technicalParameters.values.alias","size":1000,"order":{"_key":"asc"}},"aggs":{"value_name":{"top_hits":{"size":1,"_source":{"includes":["technicalParameters.values.name.cs_CZ","technicalParameters.values.position"]}}},"total_count":{"cardinality":{"field":"id","precision_threshold":30000}}}}}},"min_number":{"min":{"field":"technicalParameters.number"}},"max_number":{"max":{"field":"technicalParameters.number"}}}}}}}}}},"brand_filtered":{"filter":{"bool":{"must":[{"term":{"show.cs_CZ":true}},{"nested":{"path":"categories","query":{"term":{"categories.id":"019c1de6-872e-700c-becb-12b4790631ea"}}}}]}},"aggs":{"by_brand":{"terms":{"field":"brand.id","size":1000,"order":{"_key":"asc"}},"aggs":{"brand_name":{"top_hits":{"size":1,"_source":{"includes":["brand.alias.cs_CZ","brand.name.cs_CZ"]}}}}}}},"price_range":{"nested":{"path":"prices.CZ_CZK"},"aggs":{"min_price":{"min":{"field":"prices.CZ_CZK.amountWithVat"}},"max_price":{"max":{"field":"prices.CZ_CZK.amountWithVat"}}}},"tags_filtered":{"filter":{"bool":{"must":[{"term":{"show.cs_CZ":true}},{"nested":{"path":"categories","query":{"term":{"categories.id":"019c1de6-872e-700c-becb-12b4790631ea"}}}}]}},"aggs":{"tags":{"nested":{"path":"tags"},"aggs":{"by_tag":{"terms":{"field":"tags.id","size":1000,"order":{"_key":"asc"}},"aggs":{"tag_info":{"top_hits":{"size":1,"sort":[{"tags.priority":{"order":"desc"}}],"_source":{"includes":["tags.name.cs_CZ","tags.color","tags.priority","tags.alias"]}}}}}}}}}},"size":0}}');

        $this->assertStringContainsString('$builder->setQuery($query);', $result);
        $this->assertStringContainsString("new NestedQuery('categories', \$subQuery);", $result);
        $this->assertStringContainsString('new FilterAggregation(', $result);
        $this->assertStringContainsString('new NestedAggregation(', $result);
        $this->assertStringContainsString('new TermsAggregation(', $result);
        $this->assertStringContainsString('new TopHitsAggregation(', $result);
        $this->assertStringContainsString('new CardinalityAggregation(', $result);
        $this->assertStringContainsString('new MinAggregation(', $result);
        $this->assertStringContainsString('new MaxAggregation(', $result);
        $this->assertStringContainsString('$builder->addAggregation(', $result);
        $this->assertStringContainsString('$builder->size(0);', $result);
    }

    public function testFulltextQueryResolvers(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{
          "query": {
            "bool": {
              "must": [
                { "match_phrase": { "name": { "query": "cerne boty", "slop": 2 } } },
                { "match_phrase_prefix": { "name": { "query": "cerne bo", "max_expansions": 10 } } },
                { "match_bool_prefix": { "name": { "query": "cerne bo", "operator": "and" } } },
                { "fuzzy": { "code": { "value": "ABC", "fuzziness": "AUTO" } } },
                { "regexp": { "code": { "value": "AB.*", "flags": "COMPLEMENT|INTERVAL" } } },
                { "ids": { "values": ["1", "2"] } },
                { "terms_set": { "tags": { "terms": ["akce"], "minimum_should_match_field": "req" } } },
                { "simple_query_string": { "query": "cerne + boty", "fields": ["name^3"], "default_operator": "and" } }
              ]
            }
          }
        }');

        $this->assertStringContainsString(
            "new MatchPhraseQuery(field: 'name', query: 'cerne boty', slop: 2);",
            $result
        );
        $this->assertStringContainsString(
            "new MatchPhrasePrefixQuery(field: 'name', query: 'cerne bo', max_expansions: 10);",
            $result
        );
        $this->assertStringContainsString(
            "new MatchBoolPrefixQuery(field: 'name', query: 'cerne bo', operator: Operator::AND);",
            $result
        );
        $this->assertStringContainsString("new FuzzyQuery(field: 'code', value: 'ABC', fuzziness: 'AUTO');", $result);
        $this->assertStringContainsString(
            "new RegexpQuery(field: 'code', value: 'AB.*', flags: [RegexpFlag::COMPLEMENT, RegexpFlag::INTERVAL]);",
            $result
        );
        $this->assertStringContainsString("new IdsQuery(values: ['1', '2']);", $result);
        $this->assertStringContainsString(
            "new TermsSetQuery(field: 'tags', terms: ['akce'], minimum_should_match_field: 'req');",
            $result
        );
        $this->assertStringContainsString(
            "new SimpleQueryStringQuery(query: 'cerne + boty', fields: ['name^3'], default_operator: Operator::AND);",
            $result
        );
    }

    public function testShorthandFulltextQuery(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{"query": {"match_phrase": {"name": "cerne boty"}}}');

        $this->assertSame("\$matchPhraseQuery = new MatchPhraseQuery(field: 'name', query: 'cerne boty');", $result);
    }

    public function testScoringQueryResolvers(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{
          "query": {
            "bool": {
              "should": [
                { "constant_score": { "filter": { "term": { "inStock": true } }, "boost": 1.5 } },
                {
                  "boosting": {
                    "positive": { "match_all": {} },
                    "negative": { "term": { "sellingDenied": true } },
                    "negative_boost": 0.2
                  }
                },
                { "distance_feature": { "field": "createdAt", "origin": "now", "pivot": "7d" } },
                { "rank_feature": { "field": "popularity", "saturation": { "pivot": 80 } } },
                { "rank_feature": { "field": "sales", "log": { "scaling_factor": 4 } } },
                { "pinned": { "organic": { "match_all": {} }, "ids": ["1", "2"] } },
                { "more_like_this": { "fields": ["name"], "like": "boty", "min_term_freq": 1 } }
              ]
            }
          }
        }');

        $this->assertStringContainsString("new ConstantScoreQuery(filter: \$constantScoreFilter, boost: 1.5);", $result);
        $this->assertStringContainsString("new BoostingQuery(positive: \$boostingPositive, negative: \$boostingNegative, negative_boost: 0.2);", $result);
        $this->assertStringContainsString("new DistanceFeatureQuery(field: 'createdAt', origin: 'now', pivot: '7d');", $result);
        $this->assertStringContainsString("new RankFeatureQuery(field: 'popularity', function: new SaturationFunction(80));", $result);
        $this->assertStringContainsString("new RankFeatureQuery(field: 'sales', function: new LogarithmFunction(4));", $result);
        $this->assertStringContainsString("new PinnedQuery(organic: \$pinnedOrganic, ids: ['1', '2']);", $result);
        $this->assertStringContainsString("new MoreLikeThisQuery(fields: ['name'], like: 'boty', min_term_freq: 1);", $result);
        // obalene query se musi vyresolvovat pred pouzitim
        $this->assertStringContainsString("\$constantScoreFilter = new TermQuery(field: 'inStock', value: true);", $result);
    }

    public function testScriptScoreQueryResolver(): void
    {
        $builder = new PhpQueryBuilder();
        $result = $builder->fromJson('{
          "query": {
            "script_score": {
              "query": { "match_all": {} },
              "script": { "source": "doc[\'popularity\'].value" },
              "min_score": 1
            }
          }
        }');

        $this->assertStringContainsString("\$scriptScoreInner = new MatchAllQuery();", $result);
        $this->assertStringContainsString("new ScriptScoreQuery(query: \$scriptScoreInner, script: [", $result);
        $this->assertStringContainsString("min_score: 1);", $result);
    }
}
