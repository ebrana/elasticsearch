<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity\Abstracted;

use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\Common\Keywords\ConstantKeywordType;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\FloatType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\Common\Numeric\LongType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;

#[Index]
abstract class AbstractAddress
{
    #[BooleanType(store: true)]
    protected string $street;

    #[ObjectType(properties: [
        new FloatType(name: "@cs"),
        new FloatType(name: "@en"),
        new FloatType(name: "@sk"),
    ])]
    protected string $manager;

    #[LongType]
    protected int $longType;

    #[FloatType]
    protected float $floatType;

    #[KeywordType(null_value: "NULL", fields: [
        new ConstantKeywordType(name: "desc1"),
        new IntegerType(name: "desc2"),
    ])]
    protected ?string $description;

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }
}
