<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Enums;

enum BoundaryScanner: string
{
    case CHARS = 'chars';
    case SENTENCE = 'sentence';
    case WORD = 'word';
}
