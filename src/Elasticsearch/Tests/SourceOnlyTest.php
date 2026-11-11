<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Indexing\Builders\DefaultDocumentBuilder;
use Elasticsearch\Indexing\Document;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Indexing\Resolvers\ScalarValueResolverTrait;
use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\Exceptions\DuplicityPropertyException;
use Elasticsearch\Mapping\Exceptions\MappingJsonCreateException;
use Elasticsearch\Mapping\Exceptions\SourceOnlyRequiresDynamicFalseException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\SourceOnly;
use Elasticsearch\Mapping\Types\Text\TextType;
use Elasticsearch\Tests\Entity\Article;
use LogicException;
use PHPUnit\Framework\TestCase;

class SourceOnlyTest extends TestCase
{
    private function loadArticle(): Index
    {
        return (new AnnotationDriver())->loadMetadata(Article::class);
    }

    /**
     * @return array{settings?: array<string, mixed>, mappings: array{dynamic?: string, properties: array<string, mixed>}}
     */
    private function mapping(Index $index): array
    {
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();
        /** @var array{settings?: array<string, mixed>, mappings: array{dynamic?: string, properties: array<string, mixed>}} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    private function sourceOnly(string $name): SourceOnly
    {
        $field = new SourceOnly(name: $name);
        $field->setFieldName($name);

        return $field;
    }

    public function testAnnotationDriverCollectsSourceOnlyFields(): void
    {
        $index = $this->loadArticle();
        $properties = $index->getProperties();

        $this->assertInstanceOf(SourceOnly::class, $properties->get('teaser'));
        $meta = $properties->get('meta');
        $this->assertInstanceOf(SourceOnly::class, $meta);
        $this->assertSame('metadata', $meta->getFieldName());
        $this->assertInstanceOf(SourceOnly::class, $properties->get('featured'));

        /** @var ObjectType $comments */
        $comments = $properties->get('comments');
        $this->assertInstanceOf(SourceOnly::class, $comments->getProperties()->get('body'));
        $this->assertInstanceOf(SourceOnly::class, $comments->getProperties()->get('extra'));
    }

    public function testSourceOnlyFieldsAreNotInTheMapping(): void
    {
        $mapping = $this->mapping($this->loadArticle())['mappings'];

        $this->assertSame('false', $mapping['dynamic'] ?? null);
        $this->assertSame(['id', 'title', 'published', 'comments'], array_keys($mapping['properties']));
        /** @var array{properties: array<string, mixed>} $comments */
        $comments = $mapping['properties']['comments'];
        $this->assertSame(['id', 'author'], array_keys($comments['properties']));
    }

    public function testDefaultDocumentBuilderWritesSourceOnlyValues(): void
    {
        $document = (new DefaultDocumentBuilder($this->loadArticle()))->build(Article::create());
        /** @var array<string, mixed> $data */
        $data = json_decode($document->toJson(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('1', $document->getId());
        $this->assertSame('Stored, not indexed', $data['teaser']);
        $this->assertSame(['author' => 'es', 'tags' => ['a', 'b'], 'views' => 10], $data['meta']);
        $this->assertArrayNotHasKey('metadata', $data);
        $this->assertTrue($data['featured']); // resolved through isFeatured()
        $this->assertArrayHasKey('nothing', $data);
        $this->assertNull($data['nothing']);
        $this->assertSame(
            [['id' => 7, 'author' => 'reader', 'body' => 'Nice one', 'extra' => ['likes' => 3]]],
            $data['comments']
        );
    }

    public function testScalarValueResolverTraitAcceptsArraysForSourceOnly(): void
    {
        $index = $this->loadArticle();
        $resolver = new class {
            use ScalarValueResolverTrait;

            public function run(DocumentInterface $document, IndexableEntityInterface $entity, Index $index): void
            {
                $this->resolveScalarByMetadata($document, $entity, $index);
            }
        };
        $document = new Document($index, '1');
        $resolver->run($document, Article::create(), $index);
        $data = $document->toArray();

        $this->assertSame('Stored, not indexed', $data['teaser']);
        $this->assertSame(['author' => 'es', 'tags' => ['a', 'b'], 'views' => 10], $data['meta']);
        $this->assertTrue($data['featured']);
        $this->assertTrue($data['published']); // mapped boolean keeps working
        $this->assertArrayNotHasKey('comments', $data); // ObjectType is skipped as before
    }

    public function testSourceOnlyOnDynamicTrueIndexIsRejected(): void
    {
        $index = new Index('dynamicke');
        $index->addProperty(new TextType(name: 'name'));
        $index->addProperty($this->sourceOnly('teaser'));

        $this->expectException(SourceOnlyRequiresDynamicFalseException::class);
        $this->expectExceptionMessage('SourceOnly field "teaser" requires dynamic "false" on its level, effective dynamic is "true"');
        (new MetadataRequestFactory())->create($index);
    }

    public function testSourceOnlyOnStrictIndexIsRejected(): void
    {
        $index = new Index('striktni', dynamic: Dynamic::STRICT);
        $index->addProperty($this->sourceOnly('teaser'));

        $this->expectException(SourceOnlyRequiresDynamicFalseException::class);
        $this->expectExceptionMessage('effective dynamic is "strict"');
        (new MetadataRequestFactory())->create($index);
    }

    public function testTheExceptionIsAMappingJsonCreateException(): void
    {
        // the bundle's profiler collector catches MappingJsonCreateException for invalid entities
        $this->assertInstanceOf(
            MappingJsonCreateException::class,
            new SourceOnlyRequiresDynamicFalseException('x', Dynamic::TRUE)
        );
    }

    public function testNestedObjectWithDynamicFalseAllowsSourceOnlyInsideADynamicIndex(): void
    {
        $object = new ObjectType(dynamic: Dynamic::FALSE, name: 'detail');
        $object->setFieldName('detail');
        $object->addProperty(new KeywordType(name: 'code'));
        $object->addProperty($this->sourceOnly('raw'));

        $index = new Index('dynamicke'); // root stays dynamic: true
        $index->addProperty($object);

        $mapping = $this->mapping($index)['mappings'];
        $this->assertArrayNotHasKey('dynamic', $mapping);
        $this->assertSame(
            ['properties' => ['code' => ['type' => 'keyword']], 'dynamic' => 'false'],
            $mapping['properties']['detail']
        );
    }

    public function testNestedObjectInheritsTheRootDynamic(): void
    {
        // ObjectType default TRUE is "not set" and inherits the root FALSE -> allowed
        $inheriting = new ObjectType(name: 'detail');
        $inheriting->setFieldName('detail');
        $inheriting->addProperty(new KeywordType(name: 'code'));
        $inheriting->addProperty($this->sourceOnly('raw'));

        $index = new Index('nedynamicke', dynamic: Dynamic::FALSE);
        $index->addProperty($inheriting);
        $this->assertSame(['code'], array_keys($this->mapping($index)['mappings']['properties']['detail']['properties']));

        // an explicit STRICT on the object overrides the root and is rejected, with the full path
        $strict = new ObjectType(dynamic: Dynamic::STRICT, name: 'strict');
        $strict->setFieldName('strict');
        $strict->addProperty(new KeywordType(name: 'code'));
        $strict->addProperty($this->sourceOnly('raw'));
        $index->addProperty($strict);

        $this->expectException(SourceOnlyRequiresDynamicFalseException::class);
        $this->expectExceptionMessage('SourceOnly field "strict.raw"');
        $this->mapping($index);
    }

    public function testMappedAndSourceOnlyFieldWithTheSameNameCollide(): void
    {
        $index = new Index('kolize', dynamic: Dynamic::FALSE);
        $index->addProperty(new TextType(name: 'teaser'));

        $this->expectException(DuplicityPropertyException::class);
        $index->addProperty($this->sourceOnly('teaser'));
    }

    public function testSourceOnlyHasNoMappingDefinition(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('SourceOnly field "teaser" is never part of the mapping.');
        $this->sourceOnly('teaser')->getCollection();
    }

    public function testDefaultDocumentBuilderRejectsObjectsInSourceOnlyField(): void
    {
        $entity = new class implements IndexableEntityInterface {
            public function getId(): int
            {
                return 1;
            }

            public function getBlob(): object
            {
                return new \stdClass();
            }
        };
        $index = new Index('objekty', dynamic: Dynamic::FALSE);
        $idType = new KeywordType(name: 'id');
        $idType->setFieldName('id');
        $index->addProperty($idType);
        $index->addProperty($this->sourceOnly('blob'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resolved value of SourceOnly field must be null, scalar or array.');
        (new DefaultDocumentBuilder($index))->build($entity);
    }
}
