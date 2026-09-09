<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Enums;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/highlighting.html
 */
enum HighlighterType: string
{
    case UNIFIED = 'unified';
    case PLAIN = 'plain';
    case FVH = 'fvh';
}
