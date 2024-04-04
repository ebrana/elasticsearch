<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\Analyzer;
use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;
use Elasticsearch\Mapping\Settings\Tokenizers\NgramTokenizer;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\Common\Numeric\IntegerType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;

#[Index(name: "Author")]
#[Analyzer(name: "trigrams", tokenizer: "ngram", filters: ["lowercase"])]
#[NgramTokenizer(name: "ngram", token_chars: [TokenChars::DIGIT])]
class Author implements IndexableEntityInterface
{
    #[IntegerType]
    private int $id;

    #[KeywordType]
    private string $name;

    /** @var Book[] */
    #[ObjectType(mappedBy: Book::class)]
    private array $books;

    private string $wrongData = '';

    /**
     * @param \Elasticsearch\Tests\Entity\Book[] $books
     */
    public function __construct(array $books)
    {
        $this->books = $books;
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

    /**
     * @return \Elasticsearch\Tests\Entity\Book[]
     */
    public function getBooks(): array
    {
        return $this->books;
    }

    public function getWrongData(): string
    {
        return $this->wrongData;
    }

    public function setWrongData(string $wrongData): void
    {
        $this->wrongData = $wrongData;
    }

    public static function create(): self
    {
        $attachment = new Attachment();
        $attachment->setId(1);
        $attachment->setName('CD');

        $book = new Book([$attachment]);
        $book->setId(1);
        $book->setName('Excellent Book');
        $book->setPublished(true);

        $author = new self([$book]);
        $author->setId(1);
        $author->setName('Elasticsearch');

        return $author;
    }
}
