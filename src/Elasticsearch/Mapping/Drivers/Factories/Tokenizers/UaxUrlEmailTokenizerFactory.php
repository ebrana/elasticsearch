<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\UaxUrlEmailTokenizer;
use stdClass;

class UaxUrlEmailTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{max_token_length?: int} $configuration
     */
    public static function create(string $name, stdClass $configuration): UaxUrlEmailTokenizer
    {
        return new UaxUrlEmailTokenizer(
            $name,
            (int)($configuration->max_token_length ?? UaxUrlEmailTokenizer::DEFAULT_MAX_TOKEN_LENGTH)
        );
    }
}
