<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\FloatType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\NestedType;
use Elasticsearch\Mapping\Types\Text\TextType;
use Elasticsearch\Tests\CustomKeyResolver;

class Attachment
{
    #[IntegerType(context: Book::class)]
    private int $id;

    #[KeywordType(context: Book::class)]
    private string $name;

    /**
     * @var float[]
     */
    #[NestedType(
        properties: [
            new FloatType(name: "@cs"),
            new FloatType(name: "@en"),
            new FloatType(name: "@sk"),
        ],
        context: Book::class
    )]
    protected array $price = [];

    /**
     * @var string[]
     */
    #[NestedType(
        keyResolver: CustomKeyResolver::class,
        fieldsTemplate: new TextType(),
        context: Book::class
    )]
    protected array $sellingPrice = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float[]
     */
    public function getPrice(): array
    {
        return $this->price;
    }

    /**
     * @return string[]
     */
    public function getSellingPrice(): array
    {
        return $this->sellingPrice;
    }
}
