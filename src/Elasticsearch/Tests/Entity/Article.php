<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use Elasticsearch\Mapping\Types\SourceOnly;
use Elasticsearch\Mapping\Types\Text\TextType;

#[Index(name: "Article", dynamic: Dynamic::FALSE)]
class Article implements IndexableEntityInterface
{
    #[IntegerType]
    private int $id = 1;

    #[TextType]
    private string $title = 'Source only';

    #[BooleanType]
    private bool $published = true;

    #[SourceOnly]
    private string $teaser = 'Stored, not indexed';

    /** @var array<string, mixed> */
    #[SourceOnly(name: "meta")]
    private array $metadata = ['author' => 'es', 'tags' => ['a', 'b'], 'views' => 10];

    #[SourceOnly]
    private bool $featured = true;

    #[SourceOnly]
    private ?string $nothing;

    /** @var Comment[] */
    #[ObjectType(mappedBy: Comment::class)]
    private array $comments;

    /**
     * @param Comment[] $comments
     */
    public function __construct(array $comments = [], ?string $nothing = null)
    {
        $this->comments = $comments;
        $this->nothing = $nothing;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function getNothing(): ?string
    {
        return $this->nothing;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    public static function create(): self
    {
        return new self([new Comment(7, 'reader', 'Nice one', ['likes' => 3])]);
    }
}
