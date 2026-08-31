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
}
