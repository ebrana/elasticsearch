<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequestFactory;
use Elasticsearch\Mapping\Types\Common\AliasType;
use Elasticsearch\Mapping\Types\Common\Numeric\ScaledFloatType;
use PHPUnit\Framework\TestCase;

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
}
