<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\KeywordTokenizer;
use stdClass;

class KeywordTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{buffer_size?: int} $configuration
     */
    public static function create(string $name, stdClass $configuration): KeywordTokenizer
    {
        return new KeywordTokenizer(
            $name,
            (int)($configuration->buffer_size ?? KeywordTokenizer::DEFAULT_BUFFER_SIZE)
        );
    }
}
