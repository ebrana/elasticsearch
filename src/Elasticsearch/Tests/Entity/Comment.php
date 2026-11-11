<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\SourceOnly;

class Comment
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        #[IntegerType(context: Article::class)]
        private int $id,
        #[KeywordType(context: Article::class)]
        private string $author,
        #[SourceOnly(context: Article::class)]
        private string $body,
        #[SourceOnly(context: Article::class, name: "extra")]
        private array $attributes,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
