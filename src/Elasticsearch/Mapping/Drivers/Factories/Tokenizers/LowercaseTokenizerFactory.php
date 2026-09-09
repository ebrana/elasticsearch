<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\LowercaseTokenizer;
use stdClass;

class LowercaseTokenizerFactory implements TokenizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): LowercaseTokenizer
    {
        return new LowercaseTokenizer($name);
    }
}
