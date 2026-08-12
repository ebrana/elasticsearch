<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\JsonDriver;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
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

    private function createIndex(Index $index): Index
    {
        $text = new TextType(name: 'name');
        $index->addProperty($text);

        return $index;
    }

    public function testMaxResultWindowSurvivesWithoutAnalysis(): void
    {
        // dokud resolveSettings() koncil na null, kdyz index nemel analysis,
        // max_result_window se do requestu nikdy nedostal
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

        $this->assertSame([
            'max_result_window'  => 20000,
            'number_of_shards'   => 3,
            'number_of_replicas' => 2,
            'refresh_interval'   => '30s',
            'max_ngram_diff'     => 5,
            'max_shingle_diff'   => 4,
        ], $this->requestSettings($index));
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

        // vnorene pod settings.index
        $this->assertSame(1, $index->getNumberOfShards());
        $this->assertSame(1, $index->getNumberOfReplicas());
        $this->assertSame(2, $index->getMaxNgramDiff());
        $this->assertSame(4, $index->getMaxShingleDiff());
        // naplocho v settings
        $this->assertSame('5s', $index->getRefreshInterval());
        $this->assertSame(20000, $index->getMaxResultWindow());

        $settings = $this->requestSettings($index);
        $this->assertSame(1, $settings['number_of_shards']);
        $this->assertSame('5s', $settings['refresh_interval']);
        $this->assertSame(20000, $settings['max_result_window']);
        // analysis zustava vedle nastaveni indexu
        $this->assertArrayHasKey('analysis', $settings);
    }
}
