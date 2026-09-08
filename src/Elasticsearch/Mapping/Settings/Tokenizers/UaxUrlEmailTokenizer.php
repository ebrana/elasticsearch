<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Jako standard, ale URL a e-maily nechava jako jeden token.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-uaxurlemail-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class UaxUrlEmailTokenizer extends AbstractTokenizer
{
    public const int DEFAULT_MAX_TOKEN_LENGTH = 255;

    public function __construct(
        string $name,
        private int $max_token_length = self::DEFAULT_MAX_TOKEN_LENGTH,
    ) {
        parent::__construct($name, 'uax_url_email');
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

        if (self::DEFAULT_MAX_TOKEN_LENGTH !== $this->max_token_length) {
            $data['max_token_length'] = $this->max_token_length;
        }

        return $data;
    }
}
