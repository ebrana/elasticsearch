<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\ClassicTokenizer;
use stdClass;

class ClassicTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{max_token_length?: int} $configuration
     */
    public static function create(string $name, stdClass $configuration): ClassicTokenizer
    {
        return new ClassicTokenizer(
            $name,
            (int)($configuration->max_token_length ?? ClassicTokenizer::DEFAULT_MAX_TOKEN_LENGTH)
        );
    }
}
