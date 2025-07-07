<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

interface SortInterface
{
    public function toArray(): Generator;
}
