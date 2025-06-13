<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;

#[Index(name: "Book")]
class Book
{
    #[IntegerType(context: Author::class)]
    private int $id;

    #[KeywordType(context: Author::class)]
    private string $name;

    #[BooleanType(context: Author::class)]
    private bool $published = false;

    /** @var Attachment[] */
    #[ObjectType(context: Author::class, mappedBy: Attachment::class)]
    private array $attachments;

    #[IntegerType(context: Product::class)]
    private int $price = 0;

    #[KeywordType(context: Product::class)]
    private string $currency = 'CZK';

    /**
     * @param \Elasticsearch\Tests\Entity\Attachment[] $attachments
     */
    public function __construct(array $attachments)
    {
        $this->attachments = $attachments;
    }

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

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @return \Elasticsearch\Tests\Entity\Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
