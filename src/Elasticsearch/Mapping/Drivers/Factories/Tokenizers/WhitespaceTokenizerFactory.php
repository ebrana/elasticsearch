<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\WhitespaceTokenizer;
use stdClass;

class WhitespaceTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{max_token_length?: int} $configuration
     */
    public static function create(string $name, stdClass $configuration): WhitespaceTokenizer
    {
        return new WhitespaceTokenizer(
            $name,
            (int)($configuration->max_token_length ?? WhitespaceTokenizer::DEFAULT_MAX_TOKEN_LENGTH)
        );
    }
}
