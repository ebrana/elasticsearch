<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

interface Query
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
