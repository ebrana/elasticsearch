<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Returns the whole input as a single token - useful when a filter chain still has to run
 * over an unanalyzed value.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-keyword-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class KeywordTokenizer extends AbstractTokenizer
{
    public const int DEFAULT_BUFFER_SIZE = 256;

    public function __construct(
        string $name,
        private int $buffer_size = self::DEFAULT_BUFFER_SIZE,
    ) {
        parent::__construct($name, 'keyword');
    }

    public function getBufferSize(): int
    {
        return $this->buffer_size;
    }

    public function setBufferSize(int $buffer_size): void
    {
        $this->buffer_size = $buffer_size;
    }

    /**
     * @return array<string, array<string>|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_BUFFER_SIZE !== $this->buffer_size) {
            $data['buffer_size'] = $this->buffer_size;
        }

        return $data;
    }
}
