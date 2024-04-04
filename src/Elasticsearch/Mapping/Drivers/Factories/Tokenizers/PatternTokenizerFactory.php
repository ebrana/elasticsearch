<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use Elasticsearch\Mapping\Settings\Tokenizers\PatternTokenizer;
use stdClass;

class PatternTokenizerFactory implements TokenizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractTokenizer
    {
        $patternTokenizer = new PatternTokenizer($name, $configuration->pattern);
        if (isset($configuration->flags)) {
            $patternTokenizer->setFlags($configuration->flags);
        }
        if (isset($configuration->group)) {
            $patternTokenizer->setGroup($configuration->group);
        }

        return $patternTokenizer;
    }
}
