<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\AnnotationDriver;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Settings\MappingSettings;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
use Elasticsearch\Tests\Entity\Book;
use Elasticsearch\Tests\Entity\Catalog;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MappingSettingsTest extends TestCase
{
    public function testOnlySetValuesAreSerializedInDeclaredOrder(): void
    {
        $settings = new MappingSettings(
            depth_limit: 5,
            total_fields_limit: 2000,
            field_name_length_ignore_dynamic_beyond_limit: false
        );

        $this->assertFalse($settings->isEmpty());
        $this->assertSame([
            'total_fields.limit' => 2000,
            'depth.limit' => 5,
            'field_name_length.ignore_dynamic_beyond_limit' => false,
        ], $settings->toArray());
    }

    public function testEveryParameterHasItsPath(): void
    {
        $settings = new MappingSettings(
            total_fields_limit: 1,
            total_fields_ignore_dynamic_beyond_limit: true,
            depth_limit: 2,
            nested_fields_limit: 3,
            nested_parents_limit: 4,
            nested_objects_limit: 5,
            field_name_length_limit: 6,
            field_name_length_ignore_dynamic_beyond_limit: false,
            dimension_fields_limit: 7
        );

        $this->assertSame(array_keys(MappingSettings::PATHS), array_keys($settings->toArray()));
        $this->assertSame(1, $settings->getTotalFieldsLimit());
        $this->assertTrue($settings->getTotalFieldsIgnoreDynamicBeyondLimit());
        $this->assertSame(2, $settings->getDepthLimit());
        $this->assertSame(3, $settings->getNestedFieldsLimit());
        $this->assertSame(4, $settings->getNestedParentsLimit());
        $this->assertSame(5, $settings->getNestedObjectsLimit());
        $this->assertSame(6, $settings->getFieldNameLengthLimit());
        $this->assertFalse($settings->getFieldNameLengthIgnoreDynamicBeyondLimit());
        $this->assertSame(7, $settings->getDimensionFieldsLimit());
    }

    public function testSettersAndNullUnset(): void
    {
        $settings = new MappingSettings(total_fields_limit: 1000);
        $settings->setTotalFieldsLimit(3000);
        $settings->setNestedObjectsLimit(500);
        $settings->setNestedObjectsLimit(null);

        $this->assertSame(['total_fields.limit' => 3000], $settings->toArray());
        $this->assertNull($settings->getNestedObjectsLimit());

        $settings->setTotalFieldsLimit(null);
        $this->assertTrue($settings->isEmpty());
    }

    public function testUnsupportedInReportsSettingsNewerThanTheServer(): void
    {
        $settings = new MappingSettings(
            total_fields_limit: 2000,
            total_fields_ignore_dynamic_beyond_limit: true,
            nested_parents_limit: 60,
            field_name_length_ignore_dynamic_beyond_limit: true
        );

        $this->assertSame([
            'total_fields.ignore_dynamic_beyond_limit' => '8.13.0',
            'nested_parents.limit' => '9.0.0',
            'field_name_length.ignore_dynamic_beyond_limit' => '9.3.0',
        ], $settings->unsupportedIn('8.12.2'));
        $this->assertSame([
            'nested_parents.limit' => '9.0.0',
            'field_name_length.ignore_dynamic_beyond_limit' => '9.3.0',
        ], $settings->unsupportedIn('8.19.0'));
        $this->assertSame(
            ['field_name_length.ignore_dynamic_beyond_limit' => '9.3.0'],
            $settings->unsupportedIn('9.2.1')
        );
        $this->assertSame([], $settings->unsupportedIn('9.3.1'));
        // only set values count
        $this->assertSame([], (new MappingSettings(total_fields_limit: 10))->unsupportedIn('8.0.0'));
    }

    public function testUnknownPathIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // exists from ES 9.5 only, the library supports up to 9.3
        $this->expectExceptionMessage('index.mapping.array_objects.limit');
        (new MappingSettings())->set('array_objects.limit', 50000);
    }

    public function testWrongValueTypeIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('expects bool, int given');
        (new MappingSettings())->set(MappingSettings::TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT, 1);
    }

    public function testAnnotationDriverCollectsTheAttribute(): void
    {
        $index = (new AnnotationDriver())->loadMetadata(Catalog::class);

        $settings = $index->getMappingSettings();
        $this->assertNotNull($settings);
        $this->assertSame(2000, $settings->getTotalFieldsLimit());
        $this->assertSame(5, $settings->getDepthLimit());
        $this->assertTrue($settings->getTotalFieldsIgnoreDynamicBeyondLimit());
        $this->assertSame(Dynamic::FALSE, $index->getDynamic());

        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();
        /** @var array{settings: array<string, mixed>, mappings: array<string, mixed>} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame([
            'max_result_window' => 10000,
            'mapping.total_fields.limit' => 2000,
            'mapping.total_fields.ignore_dynamic_beyond_limit' => true,
            'mapping.depth.limit' => 5,
        ], $decoded['settings']);
        $this->assertSame('false', $decoded['mappings']['dynamic']);
    }

    public function testIndexWithoutTheAttributeHasNoMappingSettings(): void
    {
        $driver = new AnnotationDriver([CustomKeyResolver::class => new CustomKeyResolver()]);
        $index = $driver->loadMetadata(Book::class);

        $this->assertNull($index->getMappingSettings());
    }
}
