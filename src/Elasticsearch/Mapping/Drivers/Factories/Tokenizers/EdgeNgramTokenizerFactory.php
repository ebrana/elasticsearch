<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\EdgeNgramTokenizer;
use stdClass;

class EdgeNgramTokenizerFactory implements TokenizerFactoryInterface
{
    use GramTokenizerFactoryTrait;

    public static function create(string $name, stdClass $configuration): AbstractTokenizer
    {
        $tokenizer = new EdgeNgramTokenizer($name);
        self::resolveConfiguration($tokenizer, $configuration);

        return $tokenizer;
    }
}
