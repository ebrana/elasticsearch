<?php

declare(strict_types=1);

use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\Connection\Connection;
use Elasticsearch\Connection\Params\IndexDocumentParams;
use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use Elasticsearch\Tests\LangKeyResolver;

require_once __DIR__ . '/../vendor/autoload.php';

$driver = new AnnotationDriver();
$driver->setKeyResolver(new LangKeyResolver());
$factory = new MappingMetadataFactory($driver, [Product::class, Author::class]);
$provider = new MappingMetadataProvider($factory);
$metadata = $provider->getMappingMetadata()->getMetadata();
/** @var \Elasticsearch\Mapping\Index $metadataProductIndex */
$metadataProductIndex = $metadata->get(Product::class);
/** @var \Elasticsearch\Mapping\Index $metadataAuthorIndex */
$metadataAuthorIndex = $metadata->get(Author::class);

$metadataRequestFactory = new MetadataRequestFactory();
$metadataProductRequest = $metadataRequestFactory->create($metadataProductIndex);
$metadataAuthorRequest = $metadataRequestFactory->create($metadataAuthorIndex);

$client = new Connection(ClientBuilder::create()->setHosts(['ebr-elasticsearch:9200']), 'testing_');
$client->createIndex($metadataProductRequest);
$client->createIndex($metadataAuthorRequest);
if (
    $client->hasIndex($metadataProductIndex, new IndexDocumentParams(refresh: true)) &&
    $client->hasIndex($metadataAuthorIndex)
) {
    echo 'Index ready...' . PHP_EOL;
}
