<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\Connection\Connection;
use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Search\Aggregations\SumAggregation;
use Elasticsearch\Search\Queries\BoolQuery;
use Elasticsearch\Search\Queries\Enums\BoolType;
use Elasticsearch\Search\Queries\NestedQuery;
use Elasticsearch\Search\Queries\RangeQuery;
use Elasticsearch\Search\SearchBuilderFactory;
use Elasticsearch\Search\Sorts\Sort;
use Elasticsearch\Search\Sorts\SortDirection;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use Elasticsearch\Tests\LangKeyResolver;

$driver = new AnnotationDriver();
$driver->setDefaultKeyResolver(new LangKeyResolver());
$factory = new MappingMetadataFactory($driver, [Product::class, Author::class]);
$provider = new MappingMetadataProvider($factory);
$metadata = $provider->getMappingMetadata();

$searchBuilderFactory = new SearchBuilderFactory($provider, 'testing_');

$builder = $searchBuilderFactory->create(Product::class);
$boolQuery = new BoolQuery();
$boolQuery->add((new RangeQuery('sellingPrice.@cs'))->gte('1000'), BoolType::FILTER);
$boolQuery->add((new RangeQuery('sellingPrice.@cs'))->lte('2000'), BoolType::FILTER);
$nestedPath = new NestedQuery('sellingPrice', $boolQuery);
$builder->setQuery($nestedPath);
// This query use, when sellingPrice is ObjectType (not Nested type)
//$builder->setQuery($boolQuery);

$builder->addAggregation(new SumAggregation('sum', 'sellingPrice.@cs'));
$builder->addSort(new Sort('parameters', SortDirection::ASC));

$client = new Connection(ClientBuilder::create()->setHosts(['ebr-elasticsearch:9200']), 'testing_');
echo 'Document count: ' . $client->count($searchBuilderFactory->create(Product::class)) . PHP_EOL;

$records = $client->search($builder);
$hits = $records->getHits();

if (false === $hits->isEmpty()) {
    foreach ($hits as $hit) {
        // This array has this keys: {_index: string, _id: string, _score: ?int, _source: ?array, sort: array}
        // @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-search.html
        print_r($hit['_source']);
        print_r($records->getAggregations()->toArray());
    }
} else {
    echo 'No results...' . $hits->getTotalValue();
}

$records = $client->search($builder);
$hits = $records->getHits();

echo 'Author document count: ' . $client->count($searchBuilderFactory->create(Author::class)) . PHP_EOL;
