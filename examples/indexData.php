<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\Connection\Connection;
use Elasticsearch\Indexing\Builders\DefaultDocumentBuilderFactory;
use Elasticsearch\Indexing\DocumentFactory;
use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Tests\Builders\ProductDocumentBuilderFactory;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use Elasticsearch\Tests\LangKeyResolver;

$product = Product::create();
$author = Author::create();

$driver = new AnnotationDriver();
$driver->setKeyResolver(new LangKeyResolver());
$factory = new MappingMetadataFactory($driver, [Product::class, Author::class]);
$provider = new MappingMetadataProvider($factory);

$documentFactory = new DocumentFactory($provider);
$documentFactory->addBuilderFactory(new DefaultDocumentBuilderFactory());
$documentFactory->addBuilderFactory(new ProductDocumentBuilderFactory());

$document = $documentFactory->create($product);
$document2 = $documentFactory->create($author);

$client = new Connection(ClientBuilder::create()->setHosts(['ebr-elasticsearch:9200']), 'testing_');
$client->indexDocument($document);
$client->indexDocument($document2);
