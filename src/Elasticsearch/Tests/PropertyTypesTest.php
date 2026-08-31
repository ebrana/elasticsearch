<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\Factories\Properties\AliasTypeFactory;
use Elasticsearch\Mapping\Drivers\Factories\Properties\Numeric\ScaledFloatTypeFactory;
use Elasticsearch\Mapping\Drivers\JsonDriver;
use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Types\Common\AliasType;
use Elasticsearch\Mapping\Types\Common\Dates\DateType;
use Elasticsearch\Mapping\Types\Common\Keywords\IcuCollationKeywordType;
use Elasticsearch\Mapping\Types\Spatial\GeoPointType;
use Elasticsearch\Mapping\Types\Specialized\RankFeatureType;
use Elasticsearch\Mapping\Types\Text\CompletionType;
use Elasticsearch\Mapping\Types\Common\Numeric\ScaledFloatType;
use PHPUnit\Framework\TestCase;
use stdClass;

class PropertyTypesTest extends TestCase
{
    public function testScaledFloatEmitsScalingFactor(): void
    {
        // scaling_factor je u scaled_float povinny; dokud se neemitoval, ES odmitl
        // vytvorit index s takovou property
        $type = new ScaledFloatType(scaling_factor: 100.0, name: 'price');

        $this->assertSame(
            ['type' => 'scaled_float', 'scaling_factor' => 100.0],
            $type->getCollection()->toArray()
        );
    }

    public function testScaledFloatKeepsNumericOptions(): void
    {
        $type = new ScaledFloatType(scaling_factor: 1000.0, index: false, store: true, name: 'price');

        $this->assertSame([
            'type'           => 'scaled_float',
            'index'          => false,
            'store'          => true,
            'scaling_factor' => 1000.0,
        ], $type->getCollection()->toArray());
    }

    public function testScaledFloatReachesTheMapping(): void
    {
        $index = new Index('scaled');
        $index->addProperty(new ScaledFloatType(scaling_factor: 100.0, name: 'price'));

        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();

        $this->assertStringContainsString('"scaling_factor":100', $json);
    }

    public function testAliasTypeAcceptsContext(): void
    {
        // konstruktor mel preklep "contect", takze pojmenovany argument context: padal
        $type = new AliasType(path: 'original.name', name: 'alias', context: self::class);

        $this->assertSame(self::class, $type->getContext());
        $this->assertSame(['type' => 'alias', 'path' => 'original.name'], $type->getCollection()->toArray());
    }

    public function testJsonDriverResolvesAllRegisteredTypes(): void
    {
        $index = (new JsonDriver())->loadMetadata(__DIR__ . '/Json/types.json');
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();

        /** @var array{mappings: array{properties: array<string, array<string, mixed>>}} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $properties = $decoded['mappings']['properties'];

        // dokud typy nebyly registrovane, JSON driver je zahazoval uplne
        $this->assertCount(19, $properties);

        $this->assertSame(['type' => 'match_only_text'], $properties['strucne']);
        $this->assertSame(['type' => 'constant_keyword', 'value' => 'produkt'], $properties['vzdy']);
        $this->assertSame(['type' => 'wildcard', 'ignore_above' => 100], $properties['vzor']);
        $this->assertSame(['type' => 'long', 'null_value' => '0'], $properties['velke']);
        $this->assertSame(['type' => 'short'], $properties['male']);
        $this->assertSame(['type' => 'byte'], $properties['bajt']);
        $this->assertSame(['type' => 'unsigned_long'], $properties['bezznamenka']);
        $this->assertSame(['type' => 'double', 'index' => false], $properties['presna']);
        $this->assertSame(['type' => 'half_float'], $properties['polovicni']);
        // cele cislo projde json_encode/decode jako int, ES to bere
        $this->assertSame(['type' => 'scaled_float', 'scaling_factor' => 100], $properties['skalovana']);
        $this->assertSame(['type' => 'binary', 'store' => true], $properties['priloha']);
        $this->assertSame(['type' => 'alias', 'path' => 'nazev'], $properties['alias_nazvu']);
    }

    public function testDateTypesKeepTheirParameters(): void
    {
        $index = (new JsonDriver())->loadMetadata(__DIR__ . '/Json/types.json');
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();

        /** @var array{mappings: array{properties: array<string, array<string, mixed>>}} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $properties = $decoded['mappings']['properties'];

        // format je u datumu nejcastejsi parametr; DateType ho dosud vubec neznal
        $this->assertSame([
            'type'             => 'date',
            'format'           => 'yyyy-MM-dd',
            'ignore_malformed' => true,
        ], $properties['vytvoreno']);

        $this->assertSame([
            'type'   => 'date_nanos',
            'format' => 'strict_date_optional_time_nanos',
        ], $properties['presny_cas']);
    }

    public function testDateTypeThroughAttribute(): void
    {
        $type = new DateType(name: 'createdAt', format: 'yyyy-MM-dd', index: false, store: true);

        $this->assertSame([
            'type'   => 'date',
            'format' => 'yyyy-MM-dd',
            'index'  => false,
            'store'  => true,
        ], $type->getCollection()->toArray());
    }

    public function testScaledFloatFactoryRequiresScalingFactor(): void
    {
        $this->expectException(AttributeMissingException::class);

        ScaledFloatTypeFactory::create('price', new stdClass());
    }

    public function testAliasFactoryRequiresPath(): void
    {
        $this->expectException(AttributeMissingException::class);

        AliasTypeFactory::create('alias', new stdClass());
    }

    public function testCompletionType(): void
    {
        $this->assertSame(['type' => 'completion'], (new CompletionType(name: 'doplneni'))->getCollection()->toArray());

        $type = new CompletionType(
            analyzer: 'simple',
            preserve_separators: false,
            max_input_length: 30,
            contexts: [['name' => 'kategorie', 'type' => 'category', 'path' => 'cat']],
            name: 'doplneni'
        );

        $this->assertSame([
            'type'                => 'completion',
            'analyzer'            => 'simple',
            'preserve_separators' => false,
            'max_input_length'    => 30,
            'contexts'            => [['name' => 'kategorie', 'type' => 'category', 'path' => 'cat']],
        ], $type->getCollection()->toArray());
    }

    public function testRankFeatureType(): void
    {
        $this->assertSame(
            ['type' => 'rank_feature'],
            (new RankFeatureType(name: 'popularita'))->getCollection()->toArray()
        );

        // false se posilat musi - vyssi hodnota ma skore snizovat
        $this->assertSame(
            ['type' => 'rank_feature', 'positive_score_impact' => false],
            (new RankFeatureType(positive_score_impact: false, name: 'doba'))->getCollection()->toArray()
        );
    }

    public function testGeoPointType(): void
    {
        $this->assertSame(
            ['type' => 'geo_point'],
            (new GeoPointType(name: 'pozice'))->getCollection()->toArray()
        );

        $type = new GeoPointType(
            ignore_malformed: true,
            ignore_z_value: false,
            null_value: ['lat' => 0.0, 'lon' => 0.0],
            index: false,
            name: 'pozice'
        );

        $this->assertSame([
            'type'             => 'geo_point',
            'ignore_malformed' => true,
            'ignore_z_value'   => false,
            'null_value'       => ['lat' => 0.0, 'lon' => 0.0],
            'index'            => false,
        ], $type->getCollection()->toArray());
    }

    public function testIcuCollationKeywordType(): void
    {
        $type = new IcuCollationKeywordType(
            language: 'cs',
            country: 'CZ',
            strength: 'primary',
            numeric: true,
            index: false,
            name: 'razeni'
        );

        $this->assertSame([
            'type'     => 'icu_collation_keyword',
            'language' => 'cs',
            'country'  => 'CZ',
            'strength' => 'primary',
            'numeric'  => true,
            'index'    => false,
        ], $type->getCollection()->toArray());
    }

    public function testJsonDriverResolvesSearchTypes(): void
    {
        $index = (new JsonDriver())->loadMetadata(__DIR__ . '/Json/searchTypes.json');
        $json = (new MetadataRequestFactory())->create($index)->getMappingJson();

        /** @var array{mappings: array{properties: array<string, array<string, mixed>>}} $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $properties = $decoded['mappings']['properties'];

        $this->assertSame(['type' => 'completion', 'max_input_length' => 30], $properties['doplneni']);
        $this->assertSame(['type' => 'rank_feature'], $properties['popularita']);
        $this->assertSame(
            ['type' => 'rank_feature', 'positive_score_impact' => false],
            $properties['doba_doruceni']
        );
        $this->assertSame(['type' => 'geo_point', 'ignore_malformed' => true], $properties['pozice']);
        $this->assertSame(
            ['type' => 'icu_collation_keyword', 'language' => 'cs', 'country' => 'CZ', 'index' => false],
            $properties['razeni']
        );
    }
}
