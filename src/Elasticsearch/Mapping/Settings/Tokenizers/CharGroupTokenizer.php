<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Splits text at the given characters. A cheaper alternative to the pattern tokenizer
 * when listing the separators is enough.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-chargroup-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class CharGroupTokenizer extends AbstractTokenizer
{
    public const int DEFAULT_MAX_TOKEN_LENGTH = 255;

    /**
     * tokenize_on_chars can hold both character class names (whitespace, letter, digit,
     * punctuation, symbol) and concrete characters ("-", "/").
     *
     * @param string[] $tokenize_on_chars
     */
    public function __construct(
        string $name,
        private array $tokenize_on_chars,
        private int $max_token_length = self::DEFAULT_MAX_TOKEN_LENGTH,
    ) {
        parent::__construct($name, 'char_group');
    }

    /**
     * @return string[]
     */
    public function getTokenizeOnChars(): array
    {
        return $this->tokenize_on_chars;
    }

    public function addTokenizeOnChar(string $value): void
    {
        $this->tokenize_on_chars[] = $value;
    }

    public function getMaxTokenLength(): int
    {
        return $this->max_token_length;
    }

    public function setMaxTokenLength(int $max_token_length): void
    {
        $this->max_token_length = $max_token_length;
    }

    /**
     * @return array<string, array<string>|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['tokenize_on_chars'] = $this->tokenize_on_chars;

        if (self::DEFAULT_MAX_TOKEN_LENGTH !== $this->max_token_length) {
            $data['max_token_length'] = $this->max_token_length;
        }

        return $data;
    }
}
