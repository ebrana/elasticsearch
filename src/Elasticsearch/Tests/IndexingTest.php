<?php declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Indexing\Builders\DefaultDocumentBuilderFactory;
use Elasticsearch\Indexing\DocumentFactory;
use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\Text\TextType;
use Elasticsearch\Tests\Builders\ProductDocumentBuilderFactory;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Book;
use Elasticsearch\Tests\Entity\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IndexingTest extends TestCase
{
    public function testCreateDocument(): void
    {
        $product = Product::create();

        $driver = new AnnotationDriver();
        $driver->setKeyResolver(new LangKeyResolver());
        $factory = new MappingMetadataFactory($driver, [Product::class]);
        $provider = new MappingMetadataProvider($factory);

        $documentFactory = new DocumentFactory($provider);
        $documentFactory->addBuilderFactory(new ProductDocumentBuilderFactory());

        $document = $documentFactory->create($product);
        /** @var mixed[] $array */
        $array = json_decode($document->toJson(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('pk', $array);
        $this->assertEquals('test', $array['pk']);
    }

    public function testCreateDocumentByClassBuilder(): void
    {
        $author = Author::create();

        $driver = new AnnotationDriver();
        $driver->setKeyResolver(new LangKeyResolver());
        $factory = new MappingMetadataFactory($driver, [Author::class]);
        $provider = new MappingMetadataProvider($factory);

        $documentFactory = new DocumentFactory($provider);
        $documentFactory->addBuilderFactory(new DefaultDocumentBuilderFactory());

        $document = $documentFactory->create($author);
        /** @var mixed[][][][][] $array */
        $array = json_decode($document->toJson(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('id', $array);
        $this->assertEquals('1', $array['id']);
        $this->assertCount(1, $array['books']);
        $this->assertCount(1, $array['books'][0]['attachments']);
        $this->assertEquals('CD', $array['books'][0]['attachments'][0]['name']);
    }

    public function testCreateDocumentByClassBuilderAndWrongDataForResolver(): void
    {
        $author = Author::create();

        $driver = new AnnotationDriver();
        $driver->setKeyResolver(new LangKeyResolver());
        $factory = new MappingMetadataFactory($driver, [Author::class]);
        $provider = new MappingMetadataProvider($factory);

        /** @var \Elasticsearch\Mapping\Index $mapping */
        $mapping = $provider->getMappingMetadata()->getIndexByClasss(Author::class);
        $objectType = new ObjectType(mappedBy: Book::class);
        $objectType->setName('wrongData');
        $objectType->setFieldName('wrongData');
        $objectType->addProperty(new TextType(name: 'wrongData'));
        $mapping->addProperty($objectType);

        $documentFactory = new DocumentFactory($provider);
        $documentFactory->addBuilderFactory(new DefaultDocumentBuilderFactory());

        $this->expectException(InvalidArgumentException::class);
        $documentFactory->create($author);
    }
}
