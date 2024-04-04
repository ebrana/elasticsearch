<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\NgramTokenizer;
use stdClass;

class NgramTokenizerFactory implements TokenizerFactoryInterface
{
    use GramTokenizerFactoryTrait;

    public static function create(string $name, stdClass $configuration): AbstractTokenizer
    {
        $tokenizer = new NgramTokenizer($name);
        self::resolveConfiguration($tokenizer, $configuration);

        return $tokenizer;
    }
}
