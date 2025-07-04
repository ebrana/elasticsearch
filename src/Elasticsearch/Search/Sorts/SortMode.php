<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

enum SortMode: string
{
    case AVG = 'avg';
    case MAX = 'max';
    case MIN = 'min';
    case SUM = 'sum';
    case MEDIAN = 'median';
}
