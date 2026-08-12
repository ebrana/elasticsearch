<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Highlight\Enums;

/**
 * Pouziva se jen u plain highlighteru.
 */
enum Fragmenter: string
{
    case SIMPLE = 'simple';
    case SPAN = 'span';
}
