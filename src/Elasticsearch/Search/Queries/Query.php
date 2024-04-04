<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Generator;

interface Query
{
    public function toArray(): Generator;
}
