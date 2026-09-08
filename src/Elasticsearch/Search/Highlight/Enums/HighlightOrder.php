<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Enums;

enum HighlightOrder: string
{
    case NONE = 'none';
    case SCORE = 'score';
}
