<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\Tokenizers\CharGroupTokenizer;
use stdClass;

class CharGroupTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{tokenize_on_chars?: string[], max_token_length?: int} $configuration
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): CharGroupTokenizer
    {
        if (!isset($configuration->tokenize_on_chars)) {
            throw new AttributeMissingException('Char Group tokenizer must define tokenize_on_chars.');
        }

        return new CharGroupTokenizer(
            $name,
            $configuration->tokenize_on_chars,
            (int)($configuration->max_token_length ?? CharGroupTokenizer::DEFAULT_MAX_TOKEN_LENGTH)
        );
    }
}
