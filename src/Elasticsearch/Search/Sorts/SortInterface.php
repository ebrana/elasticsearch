<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

interface SortInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
