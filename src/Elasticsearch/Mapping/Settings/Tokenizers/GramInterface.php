<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;

interface GramInterface
{
    public function setMinGram(int $min_gram): void;

    public function setMaxGram(int $max_gram): void;

    /** @param TokenChars[] $token_chars */
    public function setTokenChars(array $token_chars): void;

    /** @param string[]|null $custom_token_chars */
    public function setCustomTokenChars(?array $custom_token_chars): void;
}
