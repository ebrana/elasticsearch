<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\AbstractTokenizer;
use stdClass;

interface TokenizerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): AbstractTokenizer;
}
