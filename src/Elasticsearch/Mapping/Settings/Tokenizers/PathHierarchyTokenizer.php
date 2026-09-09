<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Z hierarchicke cesty udela token pro kazdou uroven ("/elektro/mobily/kryty" ->
 * "/elektro", "/elektro/mobily", "/elektro/mobily/kryty"). Hodi se na kategoriemi
 * filtrovane fasety.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-pathhierarchy-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class PathHierarchyTokenizer extends AbstractTokenizer
{
    public const string DEFAULT_DELIMITER = '/';
    public const int DEFAULT_BUFFER_SIZE = 1024;
    public const int DEFAULT_SKIP = 0;

    public function __construct(
        string $name,
        private string $delimiter = self::DEFAULT_DELIMITER,
        private ?string $replacement = null,
        private int $buffer_size = self::DEFAULT_BUFFER_SIZE,
        private bool $reverse = false,
        private int $skip = self::DEFAULT_SKIP,
    ) {
        parent::__construct($name, 'path_hierarchy');
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    public function getReplacement(): ?string
    {
        return $this->replacement;
    }

    public function setReplacement(?string $replacement): void
    {
        $this->replacement = $replacement;
    }

    public function getBufferSize(): int
    {
        return $this->buffer_size;
    }

    public function setBufferSize(int $buffer_size): void
    {
        $this->buffer_size = $buffer_size;
    }

    public function isReverse(): bool
    {
        return $this->reverse;
    }

    public function setReverse(bool $reverse): void
    {
        $this->reverse = $reverse;
    }

    public function getSkip(): int
    {
        return $this->skip;
    }

    public function setSkip(int $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * @return array<string, array<string>|bool|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_DELIMITER !== $this->delimiter) {
            $data['delimiter'] = $this->delimiter;
        }

        if (null !== $this->replacement) {
            $data['replacement'] = $this->replacement;
        }

        if (self::DEFAULT_BUFFER_SIZE !== $this->buffer_size) {
            $data['buffer_size'] = $this->buffer_size;
        }

        if ($this->reverse) {
            $data['reverse'] = true;
        }

        if (self::DEFAULT_SKIP !== $this->skip) {
            $data['skip'] = $this->skip;
        }

        return $data;
    }
}
