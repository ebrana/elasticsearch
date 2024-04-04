<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\Enums\TokenChars;
use Elasticsearch\Mapping\Settings\Tokenizers\GramInterface;
use stdClass;

trait GramTokenizerFactoryTrait
{
    private static function resolveConfiguration(
        GramInterface $tokenizer,
        stdClass $configuration
    ): void
    {
        if (isset($configuration->min_gram)) {
            $tokenizer->setMinGram($configuration->min_gram);
        }
        if (isset($configuration->max_gram)) {
            $tokenizer->setMaxGram($configuration->max_gram);
        }
        if (isset($configuration->token_chars)) {
            $token_chars = [];
            foreach ($configuration->token_chars as $token_char) {
                $token_chars[] = TokenChars::from($token_char);
            }
            $tokenizer->setTokenChars($token_chars);
        }
        if (isset($configuration->custom_token_chars)) {
            $tokenizer->setCustomTokenChars($configuration->custom_token_chars);
        }
    }
}
