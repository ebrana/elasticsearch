<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\Drivers\JsonDriver;
use Elasticsearch\Mapping\MappingMetadataFactory;
use Elasticsearch\Mapping\MappingMetadataProvider;
use Elasticsearch\Mapping\MetadataProviderInterface;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;
use Elasticsearch\Mapping\Settings\Tokenizers\NgramTokenizer;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\Common\Numeric\FloatType;
use Elasticsearch\Mapping\Types\Common\Numeric\LongType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\NestedType;
use Elasticsearch\Mapping\Types\Text\TextType;
use Elasticsearch\Tests\Entity\Address;
use Elasticsearch\Tests\Entity\Author;
use Elasticsearch\Tests\Entity\Product;
use PHPUnit\Framework\TestCase;

class MappingTest extends TestCase
{
    public function testEnums(): void
    {
        $tokenizer = new NgramTokenizer('test');
        $tokenizer->setTokenChars([TokenChars::DIGIT, TokenChars::LETTER]);
        /** @var mixed[][] $data */
        $data = $tokenizer->toArray();
        $this->assertSame('digit', $data['token_chars'][0]);
    }

    public function testReadMapping(): void
    {
        $metadata = $this->getMappingMetadata()->getMappingMetadata()->getMetadata();
        /** @var \Elasticsearch\Mapping\Index $metadataAddressIndex */
        $metadataAddressIndex = $metadata->get(Address::class);

        $floatMyType = new FloatType();
        $floatMyType->setName('myField');
        $metadataAddressIndex->addProperty($floatMyType);

        /** @var \Elasticsearch\Mapping\Types\Common\BooleanType $streetField */
        $streetField = $metadataAddressIndex->getProperties()->get('street');
        /** @var \Elasticsearch\Mapping\Types\Common\Keywords\KeywordType $descriptionField */
        $descriptionField = $metadataAddressIndex->getProperties()->get('description');
        /** @var \Elasticsearch\Mapping\Types\Common\Numeric\LongType $longType */
        $longType = $metadataAddressIndex->getProperties()->get('longType');
        /** @var \Elasticsearch\Mapping\Types\Common\Numeric\FloatType $floatType */
        $floatType = $metadataAddressIndex->getProperties()->get('floatType');
        /** @var BooleanType $booleanField */
        $booleanField = $metadataAddressIndex->getProperties()->get('isMain');

        $fields = $descriptionField->getFields();

        $this->assertInstanceOf(BooleanType::class, $streetField);
        $this->assertInstanceOf(LongType::class, $longType);
        $this->assertInstanceOf(FloatType::class, $floatType);

        $this->assertSame('long', $longType->getType());
        $this->assertSame('float', $floatType->getType());

//        /** @var \Elasticsearch\Mapping\Types\Helpers\Metadata $meta */
//        $meta = $streetField->getMeta();
//        $this->assertSame('address', $meta->getUnit());

        /** @var \Elasticsearch\Mapping\Types\Common\Keywords\ConstantKeywordType $desc1 */
        $desc1 = $fields->get('desc1');
        $this->assertSame('constant_keyword', $desc1->getType());
        $this->assertSame('isMain', $booleanField->getName());
    }

    public function testRelationByContext(): void
    {
        $metadata = $this->getMappingMetadata()->getMappingMetadata()->getMetadata();
        /** @var \Elasticsearch\Mapping\Index $metadataAuthorIndex */
        $metadataAuthorIndex = $metadata->get(Author::class);

        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $booksField */
        $booksField = $metadataAuthorIndex->getProperties()->get('books');
        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $attachmentField */
        $attachmentField = $booksField->getProperties()->get('attachments');

        $this->assertCount(4, $booksField->getProperties());
        $this->assertCount(4, $attachmentField->getProperties());

        $attachmentProperties = $attachmentField->getProperties();
        /** @var NestedType $priceField */
        $priceField = $attachmentProperties->get('price');
        /** @var NestedType $sellingPriceField */
        $sellingPriceField = $attachmentProperties->get('sellingPrice');
        $this->assertCount(3, $priceField->getProperties());
        $this->assertInstanceOf(NestedType::class, $priceField);
        $this->assertEquals(CustomKeyResolver::class, $sellingPriceField->getKeyResolver());
    }

    public function testMetadataRequest(): void
    {
        $metadata = $this->getMappingMetadata()->getMappingMetadata()->getMetadata();
        /** @var \Elasticsearch\Mapping\Index $metadataProductIndex */
        $metadataProductIndex = $metadata->get(Product::class);

        $metadataRequestFactory = new MetadataRequestFactory();
        $metadataRequest = $metadataRequestFactory->create($metadataProductIndex);

        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $objectType */
        $objectType = $metadataProductIndex->getProperties()->get('sellingPrice');
        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $objectType2 */
        $objectType2 = $metadataProductIndex->getProperties()->get('sellingPriceWithVat');
        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $objectType3 */
        $objectType3 = $metadataProductIndex->getProperties()->get('test1');
        /** @var \Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType $objectType4 */
        $objectType4 = $metadataProductIndex->getProperties()->get('test3');
        /** @var \Elasticsearch\Mapping\Types\Common\Keywords\KeywordType $keyword */
        $keyword = $metadataProductIndex->getProperties()->get('sellingPriceWithVatKeyword');
        /** @var \Elasticsearch\Mapping\Types\Text\MatchOnlyTextType $matchOnly */
        $matchOnly = $metadataProductIndex->getProperties()->get('matchOnlyText');
        /** @var NestedType $nestedByKeyResolver */
        $nestedByKeyResolver = $metadataProductIndex->getProperties()->get('books');

        $this->assertSame('amproductsmodule', $metadataRequest->getIndex()->getName());

        /** @var mixed[][][][][][][][][] $mapping */
        $mapping = json_decode($metadataRequest->getMappingJson(), true);
        $this->assertArrayHasKey('mappings', $mapping);
        $this->assertArrayHasKey('properties', $mapping['mappings']);
        $this->assertArrayHasKey('pk', $mapping['mappings']['properties']);
        $this->assertArrayHasKey('parameterValues', $mapping['mappings']['properties']);
        $this->assertArrayHasKey('sellingPriceWithVat', $mapping['mappings']['properties']);
        $this->assertArrayHasKey('postEventName', $mapping['mappings']['properties']);
        $this->assertArrayHasKey('properties', $mapping['mappings']['properties']['sellingPriceWithVat']);
        $this->assertArrayHasKey('@cs', $mapping['mappings']['properties']['sellingPriceWithVat']['properties']);
        $this->assertArrayHasKey('@en', $mapping['mappings']['properties']['sellingPriceWithVat']['properties']);
        $this->assertArrayHasKey('@sk', $mapping['mappings']['properties']['sellingPriceWithVat']['properties']);
        $this->assertArrayHasKey('settings', $mapping);
        $this->assertArrayHasKey('settings', $mapping);
        $this->assertArrayHasKey('analysis', $mapping['settings']);
        $this->assertArrayHasKey('analyzer', $mapping['settings']['analysis']);
        $this->assertCount(2, $mapping['settings']['analysis']['analyzer']);
        $this->assertArrayHasKey('standard', $mapping['settings']['analysis']['analyzer']);
        $this->assertArrayHasKey('autocomplete_analyzer', $mapping['settings']['analysis']['analyzer']);
        $this->assertArrayHasKey('filter', $mapping['settings']['analysis']);
        $this->assertArrayHasKey('tokenizer', $mapping['settings']['analysis']);
        $this->assertArrayHasKey('ngram', $mapping['settings']['analysis']['tokenizer']);
        $this->assertEquals('nested', $objectType->getType());
        $this->assertEquals('object', $objectType2->getType());
        $this->assertEquals('sellingPrice', $objectType->getName());
        $this->assertEquals('sellingPriceWithVat', $objectType2->getName());
        $this->assertEquals('test3', $objectType4->getName());
        $this->assertEquals('translations', $objectType3->getFieldName());
        $this->assertEquals('test1', $objectType3->getName());
        $this->assertArrayHasKey('@cs', $mapping['mappings']['properties']['test3']['properties']);
        $this->assertArrayHasKey('@fr', $mapping['mappings']['properties']['test3']['properties']);
        $this->assertArrayHasKey('second', $mapping['mappings']['properties']['test3']['properties']['@cs']['properties']);
        $this->assertArrayHasKey('@sk', $mapping['mappings']['properties']['test3']['properties']['@cs']['properties']['second']['properties']);
        $this->assertArrayNotHasKey('@fr', $mapping['mappings']['properties']['test3']['properties']['@cs']['properties']['second']['properties']);
        $this->assertArrayHasKey('sort_name', $mapping['mappings']['properties']['test5']['properties']['@cs']['fields']);
        $this->assertArrayHasKey('autocomplete', $mapping['mappings']['properties']['test5']['properties']['@cs']['fields']);
        $this->assertEquals('keyword', $mapping['mappings']['properties']['test5']['properties']['@cs']['fields']['sort_name']['type']);
        $this->assertEquals('text', $mapping['mappings']['properties']['test5']['properties']['@cs']['fields']['autocomplete']['type']);
        $this->assertTrue($nestedByKeyResolver->getProperties()->first()->getProperties()->containsKey('price'));
        $this->assertTrue($nestedByKeyResolver->getProperties()->first()->getProperties()->containsKey('currency'));
        $this->assertEquals('copy', $keyword->getCopyTo());
        $this->assertEquals('copy_match', $matchOnly->getCopyTo());
        $firstField = $matchOnly->getFields()->first();
        $this->assertCount(1, $matchOnly->getFields());
        $this->assertInstanceOf(TextType::class, $firstField);
        $this->assertEquals('extra_field', $firstField->getName());
        $this->assertEquals('test_unit', $matchOnly->getMeta()?->getUnit());
    }

    public function testJsonDriver(): void
    {
        $jsonTestFile = __DIR__ . '/Json/test.json';
        $driver = new JsonDriver();
        $factory = new MappingMetadataFactory($driver, [$jsonTestFile]);
        $metadataProvider = new MappingMetadataProvider($factory);
        $mapping = $metadataProvider->getMappingMetadata();

        /** @var \Elasticsearch\Mapping\Index $indexMapping */
        $indexMapping = $mapping->getIndexByClasss($jsonTestFile);
        $metadataRequestFactory = new MetadataRequestFactory();
        $metadataRequest = $metadataRequestFactory->create($indexMapping);

        /** @var mixed[][][][][][] $mapping */
        $mapping = json_decode($metadataRequest->getMappingJson(), true, 512, JSON_THROW_ON_ERROR);
        /** @var mixed[][][][][][][] $mapping2 */
        $mapping2 = json_decode((string)file_get_contents($jsonTestFile), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('text', $mapping['mappings']['properties']['searching_names']['type']);
        $this->assertEquals('pattern_replace', $mapping['settings']['analysis']['character_filter']['dots_replace_filter']['type']);
        $this->assertEquals('\.', $mapping['settings']['analysis']['character_filter']['dots_replace_filter']['pattern']);
        $this->assertEquals('custom', $mapping['settings']['analysis']['analyzer']['full_with_diacritic']['type']);
        $this->assertCount(1, $mapping['settings']['analysis']['analyzer']['full_with_diacritic']['filter']);
        $this->assertCount(53, $mapping['mappings']['properties']);
        $this->assertCount(53, $mapping2['testing_amproductsmodule']['mappings']['properties']);
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
