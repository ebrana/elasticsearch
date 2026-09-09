<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\LetterTokenizer;
use stdClass;

class LetterTokenizerFactory implements TokenizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): LetterTokenizer
    {
        return new LetterTokenizer($name);
    }
}
