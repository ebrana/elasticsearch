<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\StandardTokenizer;
use stdClass;

class StandardTokenizerFactory implements TokenizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractTokenizer
    {
        $tokenizer = new StandardTokenizer($name);
        if (isset($configuration->max_token_length)) {
            $tokenizer->setMaxTokenLength($configuration->max_token_length);
        }

        return $tokenizer;
    }
}
