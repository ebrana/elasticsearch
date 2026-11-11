<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\JsonDriver;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Settings\MappingSettings;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
use Elasticsearch\Mapping\Types\Text\TextType;
use PHPUnit\Framework\TestCase;

class IndexSettingsTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function requestSettings(Index $index): array
    {
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();
        /** @var array{settings?: array<string, mixed>} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $decoded['settings'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private function requestMappings(Index $index): array
    {
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();
        /** @var array{mappings: array<string, mixed>} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $decoded['mappings'];
    }

    private function loadJson(string $json): Index
    {
        $file = tempnam(sys_get_temp_dir(), 'es-settings-') ?: throw new \RuntimeException('cannot create temp file');
        file_put_contents($file, $json);

        try {
            return (new JsonDriver())->loadMetadata($file);
        } finally {
            unlink($file);
        }
    }

    private function createIndex(Index $index): Index
    {
        $text = new TextType(name: 'name');
        $index->addProperty($text);

        return $index;
    }

    public function testMaxResultWindowSurvivesWithoutAnalysis(): void
    {
        // while resolveSettings() returned null when the index had no analysis,
        // max_result_window never made it into the request
        $index = $this->createIndex(new Index('bezanalysis', max_result_window: 50000));

        $this->assertSame(['max_result_window' => 50000], $this->requestSettings($index));
    }

    public function testAllIndexSettings(): void
    {
        $index = $this->createIndex(new Index(
            'nastaveni',
            max_result_window: 20000,
            number_of_shards: 3,
            number_of_replicas: 2,
            refresh_interval: '30s',
            max_ngram_diff: 5,
            max_shingle_diff: 4
        ));
        $index->setMappingSettings(new MappingSettings(
            total_fields_limit: 2000,
            total_fields_ignore_dynamic_beyond_limit: false
        ));

        $this->assertSame([
            'max_result_window'  => 20000,
            'number_of_shards'   => 3,
            'number_of_replicas' => 2,
            'refresh_interval'   => '30s',
            'max_ngram_diff'     => 5,
            'max_shingle_diff'   => 4,
            'mapping.total_fields.limit' => 2000,
            'mapping.total_fields.ignore_dynamic_beyond_limit' => false,
        ], $this->requestSettings($index));
    }

    public function testEmptyMappingSettingsAddNothing(): void
    {
        $index = $this->createIndex(new Index('prazdne'));
        $index->setMappingSettings(new MappingSettings());

        $this->assertSame(['max_result_window' => 10000], $this->requestSettings($index));
    }

    public function testMappingSettingsAreMutableAfterwards(): void
    {
        // e.g. in a post-event handler
        $index = $this->createIndex(new Index('limity'));
        $index->setMappingSettings(new MappingSettings(total_fields_limit: 1000));
        $index->getMappingSettings()?->setTotalFieldsLimit(3000);
        $index->getMappingSettings()?->setNestedObjectsLimit(20000);

        $settings = $this->requestSettings($index);

        $this->assertSame(3000, $settings['mapping.total_fields.limit']);
        $this->assertSame(20000, $settings['mapping.nested_objects.limit']);
    }

    public function testDynamicTrueIsNotSent(): void
    {
        $index = $this->createIndex(new Index('dynamicke'));

        $this->assertSame(Dynamic::TRUE, $index->getDynamic());
        $this->assertArrayNotHasKey('dynamic', $this->requestMappings($index));
    }

    public function testDynamicFalseAndStrictAreSent(): void
    {
        $index = $this->createIndex(new Index('nedynamicke', dynamic: Dynamic::FALSE));
        $mappings = $this->requestMappings($index);
        $this->assertSame('false', $mappings['dynamic']);
        // properties stay next to it
        $this->assertArrayHasKey('name', $mappings['properties']);

        $index->setDynamic(Dynamic::STRICT);
        $this->assertSame('strict', $this->requestMappings($index)['dynamic']);
    }

    public function testUnsetSettingsAreOmitted(): void
    {
        $index = $this->createIndex(new Index('vychozi'));

        $this->assertSame(['max_result_window' => 10000], $this->requestSettings($index));
    }

    public function testSettersOverrideConstructor(): void
    {
        $index = $this->createIndex(new Index('setters', number_of_shards: 1));
        $index->setNumberOfShards(5);
        $index->setRefreshInterval('-1');
        $index->setMaxResultWindow(1000);

        $settings = $this->requestSettings($index);

        $this->assertSame(1000, $settings['max_result_window']);
        $this->assertSame(5, $settings['number_of_shards']);
        $this->assertSame('-1', $settings['refresh_interval']);
    }

    public function testJsonDriverReadsIndexSettings(): void
    {
        $jsonTestFile = __DIR__ . '/Json/test.json';
        $index = (new JsonDriver())->loadMetadata($jsonTestFile);

        // nested under settings.index
        $this->assertSame(1, $index->getNumberOfShards());
        $this->assertSame(1, $index->getNumberOfReplicas());
        $this->assertSame(2, $index->getMaxNgramDiff());
        $this->assertSame(4, $index->getMaxShingleDiff());
        $mappingSettings = $index->getMappingSettings();
        $this->assertNotNull($mappingSettings);
        $this->assertSame(1500, $mappingSettings->getTotalFieldsLimit());
        $this->assertTrue($mappingSettings->getTotalFieldsIgnoreDynamicBeyondLimit());
        $this->assertSame(100, $mappingSettings->getNestedFieldsLimit());
        $this->assertSame(Dynamic::FALSE, $index->getDynamic());
        // flat in settings
        $this->assertSame('5s', $index->getRefreshInterval());
        $this->assertSame(20000, $index->getMaxResultWindow());

        $settings = $this->requestSettings($index);
        $this->assertSame(1, $settings['number_of_shards']);
        $this->assertSame('5s', $settings['refresh_interval']);
        $this->assertSame(20000, $settings['max_result_window']);
        $this->assertSame(1500, $settings['mapping.total_fields.limit']);
        $this->assertTrue($settings['mapping.total_fields.ignore_dynamic_beyond_limit']);
        $this->assertSame(100, $settings['mapping.nested_fields.limit']);
        // analysis stays next to the index settings
        $this->assertArrayHasKey('analysis', $settings);
        $this->assertSame('false', $this->requestMappings($index)['dynamic']);
    }

    public function testJsonDriverReadsDottedTotalFieldsKeysAndBooleanDynamic(): void
    {
        // the shape Elasticsearch itself returns from GET _settings?flat_settings=true: dotted keys with
        // the `index.` prefix and every value as a string; `dynamic` written as a real boolean
        $index = $this->loadJson(<<<'JSON'
        {
            "dotted": {
                "settings": {
                    "index.mapping.total_fields.limit": "2000",
                    "index.mapping.total_fields.ignore_dynamic_beyond_limit": "true",
                    "index.mapping.depth.limit": "7"
                },
                "mappings": {"dynamic": false, "properties": {"name": {"type": "text"}}}
            }
        }
        JSON);

        $mappingSettings = $index->getMappingSettings();
        $this->assertNotNull($mappingSettings);
        $this->assertSame(2000, $mappingSettings->getTotalFieldsLimit());
        $this->assertTrue($mappingSettings->getTotalFieldsIgnoreDynamicBeyondLimit());
        $this->assertSame(7, $mappingSettings->getDepthLimit());
        $this->assertSame(Dynamic::FALSE, $index->getDynamic());
    }

    public function testJsonDriverReadsDottedKeysWithoutIndexPrefixAndStrictDynamic(): void
    {
        $index = $this->loadJson(<<<'JSON'
        {
            "dotted2": {
                "settings": {
                    "mapping.total_fields.limit": 1200,
                    "mapping.total_fields.ignore_dynamic_beyond_limit": "false"
                },
                "mappings": {"dynamic": "strict", "properties": {"name": {"type": "text"}}}
            }
        }
        JSON);

        $mappingSettings = $index->getMappingSettings();
        $this->assertNotNull($mappingSettings);
        $this->assertSame(1200, $mappingSettings->getTotalFieldsLimit());
        $this->assertFalse($mappingSettings->getTotalFieldsIgnoreDynamicBeyondLimit());
        $this->assertSame(Dynamic::STRICT, $index->getDynamic());
    }

    public function testJsonDriverLeavesTotalFieldsAndDynamicUnsetWhenMissing(): void
    {
        $index = $this->loadJson(<<<'JSON'
        {
            "bare": {
                "settings": {"number_of_shards": 1},
                "mappings": {"properties": {"name": {"type": "text"}}}
            }
        }
        JSON);

        $this->assertNull($index->getMappingSettings());
        $this->assertSame(Dynamic::TRUE, $index->getDynamic());
        $this->assertArrayNotHasKey('mapping.total_fields.limit', $this->requestSettings($index));
    }

    public function testNestedIndexSettingsOverrideTheFlatOnes(): void
    {
        // the same keys on both levels - the nested ones are applied last and must win
        $json = <<<'JSON'
        {
            "conflicting": {
                "settings": {
                    "number_of_shards": 9,
                    "refresh_interval": "30s",
                    "index": {
                        "number_of_shards": 3,
                        "refresh_interval": "1s"
                    }
                },
                "mappings": {"properties": {"name": {"type": "text"}}}
            }
        }
        JSON;

        $index = $this->loadJson($json);

        $this->assertSame(3, $index->getNumberOfShards());
        $this->assertSame('1s', $index->getRefreshInterval());
    }
}
