<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
