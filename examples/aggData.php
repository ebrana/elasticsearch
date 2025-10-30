<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Search\Aggregations\FilterAggregation;
use Elasticsearch\Search\Aggregations\MaxAggregation;
use Elasticsearch\Search\Aggregations\MinAggregation;
use Elasticsearch\Search\Aggregations\NestedAggregation;
use Elasticsearch\Search\Aggregations\TermsAggregation;
use Elasticsearch\Search\Aggregations\TopHitsAggregation;
use Elasticsearch\Search\Queries\BoolQuery;
use Elasticsearch\Search\Queries\Enums\BoolType;
use Elasticsearch\Search\Queries\NestedQuery;
use Elasticsearch\Search\Queries\TermQuery;
use Elasticsearch\Search\SearchBuilderFactory;
use Elasticsearch\Tests\CustomKeyResolver;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use Elasticsearch\Tests\LangKeyResolver;
use Elasticsearch\Tests\PostEventSample;

$driver = new AnnotationDriver([
    LangKeyResolver::class => new LangKeyResolver(),
    CustomKeyResolver::class => new CustomKeyResolver(),
], [
    PostEventSample::class => new PostEventSample(),
]);
$factory = new MappingMetadataFactory($driver, [Product::class, Author::class]);
$provider = new MappingMetadataProvider($factory);
$metadata = $provider->getMappingMetadata();

$searchBuilderFactory = new SearchBuilderFactory($provider, 'testing_');

$builder = $searchBuilderFactory->create(Product::class);

$nested = new NestedQuery('categories', new TermQuery('categories.id', '0198e022-00a3-71ab-8520-238feb020418'));
$boolQuery = new BoolQuery();
$boolQuery->add($nested, BoolType::FILTER);
$boolQuery->add((new TermQuery('show.cs_CZ', true)), BoolType::FILTER);

$brandAggregation = new NestedAggregation('brands', 'brands');
$termsAggregation = new TermsAggregation('by_brand', 'brands.id');
$termsAggregation->size(100000);
$termsAggregation->order(['_key' => 'asc']);
$topHitsAggregation = new TopHitsAggregation('brand_name', 1);
$topHitsAggregation->setSource(['includes' => ['brands.alias.cs_CZ']]);
$termsAggregation->aggregation($topHitsAggregation);
$brandAggregation->aggregation($termsAggregation);

$priceAggregation = new NestedAggregation('price_range', 'prices.CZ_CZK');
$minAggregation = new MinAggregation('min_price', 'prices.CZ_CZK.amountWithVat');
$maxAggregation = new MaxAggregation('max_price', 'prices.CZ_CZK.amountWithVat');
$priceAggregation->aggregation($minAggregation);
$priceAggregation->aggregation($maxAggregation);

$technicalParametersAggregation = new NestedAggregation('technical_params', 'technicalParameters');

$filteredParams = new FilterAggregation(
    'filtered_params',
    new TermQuery('technicalParameters.alias', ['barva', 'velikost', 'material'])
);

$aliasTermsAggregation = new TermsAggregation('by_param', 'technicalParameters.alias');
$aliasTermsAggregation->size(100000);
$aliasTermsAggregation->order(['_key' => 'asc']);
$tpTopHitsAggregation = new TopHitsAggregation('type', 1);
$tpTopHitsAggregation->setSource(['includes' => ['technicalParameters.type', 'technicalParameters.name']]);
$aliasTermsAggregation->aggregation($tpTopHitsAggregation);
$valuesNested = new NestedAggregation('values_nested', 'technicalParameters.values');
$valuesTermsAggregation = new TermsAggregation('by_value_alias', 'technicalParameters.values.alias');
$valuesTermsAggregation->size(100000);
$valuesTermsAggregation->order(['_key' => 'asc']);
$valueNameTopHitsAggregation = new TopHitsAggregation('value_name', 1);
$valueNameTopHitsAggregation->setSource(['includes' => ['technicalParameters.values.name.cs_CZ']]);
$valuesTermsAggregation->aggregation($valueNameTopHitsAggregation);
$valuesNested->aggregation($valuesTermsAggregation);
$aliasTermsAggregation->aggregation($valuesNested);

$tpMinAggregation = new MinAggregation('number_min', 'technicalParameters.number');
$tpMaxAggregation = new MaxAggregation('number_max', 'technicalParameters.number');
$aliasTermsAggregation->aggregation($tpMinAggregation);
$aliasTermsAggregation->aggregation($tpMaxAggregation);

$filteredParams->aggregation($aliasTermsAggregation);
$technicalParametersAggregation->aggregation($filteredParams /*$aliasTermsAggregation*/);

$builder->setQuery($boolQuery);
$builder->addAggregation($brandAggregation);
$builder->addAggregation($priceAggregation);
$builder->addAggregation($technicalParametersAggregation);
$builder->size(0);

echo json_encode($builder->getPayload()->toArray());
echo PHP_EOL;
