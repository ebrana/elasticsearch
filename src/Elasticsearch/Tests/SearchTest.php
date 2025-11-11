<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Mapping\MetadataProviderInterface;
use Elasticsearch\Search\Aggregations\GlobalAggregation;
use Elasticsearch\Search\Aggregations\SumAggregation;
use Elasticsearch\Search\Aggregations\TermsAggregation;
use Elasticsearch\Search\Collapse\Collapse;
use Elasticsearch\Search\Collapse\InnerHits;
use Elasticsearch\Search\Collapse\InnerHitsCollection;
use Elasticsearch\Search\Queries\BoolQuery;
use Elasticsearch\Search\Queries\Enums\BoolType;
use Elasticsearch\Search\Queries\Enums\MultiMatchType;
use Elasticsearch\Search\Queries\MultiMatchQuery;
use Elasticsearch\Search\Queries\RangeQuery;
use Elasticsearch\Search\Queries\TermQuery;
use Elasticsearch\Search\SearchBuilderFactory;
use Elasticsearch\Search\Sorts\GeoDistanceSort;
use Elasticsearch\Search\Sorts\NestedSort;
use Elasticsearch\Search\Sorts\ScriptSort;
use Elasticsearch\Search\Sorts\Sort;
use Elasticsearch\Search\Sorts\SortDirection;
use Elasticsearch\Search\Sorts\SortMode;
use Elasticsearch\Tests\Entity\Address;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private const INDEX_PREFIX = 'testing_';

    public function testSearch(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $boolQuery = new BoolQuery();
        $boolQuery->add((new RangeQuery('sellingPrice.@cs'))->gte('1000'), BoolType::FILTER);
        $boolQuery->add((new RangeQuery('sellingPrice.@cs'))->lte('2000'), BoolType::FILTER);
        $builder->setQuery($boolQuery);
        $builder->addAggregation(new SumAggregation('sum', 'sellingPrice.@cs'));
        $builder->addSort(new Sort('parameters', SortDirection::ASC));

        /** @var mixed[][][][][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertArrayHasKey('body', $queryCollection);
        $this->assertArrayHasKey('index', $queryCollection);
        $this->assertArrayHasKey('query', $queryCollection['body']);
        $this->assertArrayHasKey('bool', $queryCollection['body']['query']);
        $this->assertArrayHasKey('filter', $queryCollection['body']['query']['bool']);
        $this->assertArrayHasKey('range', $queryCollection['body']['query']['bool']['filter'][0]);
        $this->assertArrayHasKey('range', $queryCollection['body']['query']['bool']['filter'][1]);
        $this->assertArrayHasKey('sellingPrice.@cs', $queryCollection['body']['query']['bool']['filter'][0]['range']);
        $this->assertEquals('1000', $queryCollection['body']['query']['bool']['filter'][0]['range']['sellingPrice.@cs']['gte']);
        $this->assertArrayHasKey('aggs', $queryCollection['body']);
        $this->assertArrayHasKey('sort', $queryCollection['body']);
        $this->assertArrayHasKey('parameters', $queryCollection['body']['sort']);
        $this->assertArrayHasKey('order', $queryCollection['body']['sort']['parameters']);
        $this->assertEquals(SortDirection::ASC->value, $queryCollection['body']['sort']['parameters']['order']);
    }

    public function testAdvancedSort(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $query = new TermQuery('pk', 'test');
        $sort = new Sort('parameters', SortDirection::ASC);
        $sort->setMode(SortMode::SUM);
        $sort->setNestedSort(
            new NestedSort('sellingPrice', $query, new NestedSort('sellingPrice.@cz'))
        );
        $builder->addSort($sort);

        /** @var mixed[][][][][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertEquals(SortDirection::ASC->value, $queryCollection['body']['sort']['parameters']['order']);
        $this->assertEquals(SortMode::SUM->value, $queryCollection['body']['sort']['parameters']['mode']);
        $this->assertArrayHasKey('nested', $queryCollection['body']['sort']['parameters']);
        $this->assertEquals('sellingPrice', $queryCollection['body']['sort']['parameters']['nested']['path']);
        $this->assertArrayHasKey('filter', $queryCollection['body']['sort']['parameters']['nested']);
        $this->assertArrayHasKey('term', $queryCollection['body']['sort']['parameters']['nested']['filter']);
        $this->assertArrayHasKey('nested', $queryCollection['body']['sort']['parameters']['nested']);
        $this->assertEquals('sellingPrice.@cz', $queryCollection['body']['sort']['parameters']['nested']['nested']['path']);
        $this->assertArrayHasKey('pk', $queryCollection['body']['sort']['parameters']['nested']['filter']['term']);
        $this->assertEquals('test', $queryCollection['body']['sort']['parameters']['nested']['filter']['term']['pk']);
    }

    public function testGeoDistanceSort(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $sort = new GeoDistanceSort('POINT (-70 40)');

        $builder->addSort($sort);

        /** @var mixed[][][][][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertArrayHasKey('_geo_distance', $queryCollection['body']['sort']);
        $this->assertArrayHasKey('pin.location', $queryCollection['body']['sort']['_geo_distance']);
        $this->assertEquals('POINT (-70 40)', $queryCollection['body']['sort']['_geo_distance']['pin.location']);
        $this->assertEquals('arc', $queryCollection['body']['sort']['_geo_distance']['distance_type']);
        $this->assertEquals('m', $queryCollection['body']['sort']['_geo_distance']['unit']);
        $this->assertFalse($queryCollection['body']['sort']['_geo_distance']['ignore_unmapped']);
    }

    public function testScriptSort(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $sort = new ScriptSort('test');

        $builder->addSort($sort);

        /** @var mixed[][][][][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertArrayHasKey('_script', $queryCollection['body']['sort']);
        $this->assertEquals('test', $queryCollection['body']['sort']['_script']['script']['source']);
        $this->assertEquals('painless', $queryCollection['body']['sort']['_script']['script']['lang']);
    }

    public function testAggregation(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $boolQuery = new BoolQuery();
        $boolQuery->add((new RangeQuery('sellingPrice.@cs'))->gte('1000'), BoolType::FILTER);
        $builder->setQuery($boolQuery);
        $builder->addAggregation(new TermsAggregation('sellingPrice', 'sellingPrice.@cs'));

        /** @var mixed[][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertArrayHasKey('body', $queryCollection);
        $this->assertArrayHasKey('index', $queryCollection);
        $this->assertArrayHasKey('query', $queryCollection['body']);
        $this->assertArrayHasKey('aggs', $queryCollection['body']);
        $this->assertArrayHasKey('sellingPrice', $queryCollection['body']['aggs']);
        $this->assertArrayHasKey('terms', $queryCollection['body']['aggs']['sellingPrice']);
        $this->assertArrayHasKey('field', $queryCollection['body']['aggs']['sellingPrice']['terms']);
        $this->assertEquals('sellingPrice.@cs', $queryCollection['body']['aggs']['sellingPrice']['terms']['field']);
    }

    public function testAggregation2(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $boolQuery = new BoolQuery();
        $boolQuery->add((new RangeQuery('sellingPrice.@cs'))->gte('1000'), BoolType::FILTER);
        $builder->setQuery($boolQuery);

        $globalAggregation = new GlobalAggregation('all_docs');
        $globalAggregation->aggregation(new TermsAggregation('sellingPrice', 'sellingPrice.@cs'));

        $builder->addAggregation($globalAggregation);

        /** @var mixed[][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertEquals(json_encode($queryCollection['body']['aggs']), '{"all_docs":{"global":{},"aggs":{"sellingPrice":{"terms":{"field":"sellingPrice.@cs"}}}}}');
    }

    public function testMultimatchQuery(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $builder->setQuery(new MultiMatchQuery('this is a test', ['productTags'], MultiMatchType::PHRASE));

        /** @var mixed[][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();

        $this->assertArrayHasKey('multi_match', $queryCollection['body']['query']);
        $this->assertArrayHasKey('query', $queryCollection['body']['query']['multi_match']);
        $this->assertArrayHasKey('type', $queryCollection['body']['query']['multi_match']);
        $this->assertEquals('phrase', $queryCollection['body']['query']['multi_match']['type']);
        $this->assertEquals('this is a test', $queryCollection['body']['query']['multi_match']['query']);
    }

    public function testCollapseTest(): void
    {
        $searchBuilderFactory = new SearchBuilderFactory($this->getMappingMetadata(), self::INDEX_PREFIX);

        $builder = $searchBuilderFactory->create(Product::class);
        $innerHits = new InnerHits('innerHits', 10, 'user.id');
        $innerHitsCollection = new InnerHitsCollection();
        $innerHitsCollection->add($innerHits);
        $collapse = new Collapse('categories', $innerHitsCollection);
        $builder->setCollapse($collapse);

        /** @var mixed[][][][][] $queryCollection */
        $queryCollection = $builder->build()->toArray();
        $this->assertArrayHasKey('collapse', $queryCollection['body']);
        $this->assertArrayHasKey('inner_hits', $queryCollection['body']['collapse']);
        $this->assertArrayHasKey('innerHits', $queryCollection['body']['collapse']['inner_hits']);
        $this->assertArrayHasKey('name', $queryCollection['body']['collapse']['inner_hits']['innerHits']);
        $this->assertArrayHasKey('size', $queryCollection['body']['collapse']['inner_hits']['innerHits']);
        $this->assertArrayHasKey('collapse', $queryCollection['body']['collapse']['inner_hits']['innerHits']);
        $this->assertArrayHasKey('field', $queryCollection['body']['collapse']['inner_hits']['innerHits']['collapse']);
        $this->assertArrayNotHasKey('from', $queryCollection['body']['collapse']['inner_hits']['innerHits']);
        $this->assertEquals('categories', $queryCollection['body']['collapse']['field']);
        $this->assertEquals('user.id', $queryCollection['body']['collapse']['inner_hits']['innerHits']['collapse']['field']);
    }

    private function getMappingMetadata(): MetadataProviderInterface
    {
        $driver = new AnnotationDriver([
            LangKeyResolver::class => new LangKeyResolver(),
            CustomKeyResolver::class => new CustomKeyResolver(),
        ], [PostEventSample::class => new PostEventSample()]);
        $factory = new MappingMetadataFactory($driver, [Product::class, Address::class, Author::class]);

        return new MappingMetadataProvider($factory);
    }
}
