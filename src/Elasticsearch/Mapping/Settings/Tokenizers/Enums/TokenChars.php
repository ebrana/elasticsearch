<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers\Enums;

enum TokenChars: string
{
    case DIGIT = 'digit';
    case LETTER = 'letter';
    case WHITESPACE = 'whitespace';
    case PUNCTUATION = 'punctuation';
    case SYMBOL = 'symbol';
    case CUSTOM = 'custom';
}
