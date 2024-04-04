<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

class SortCollection
{
    /** @var Sort[] */
    private array $sorts;

    public function __construct(Sort ...$sorts)
    {
        $this->sorts = $sorts;
    }

    public function add(Sort $sort): self
    {
        $this->sorts[] = $sort;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->sorts);
    }

    public function toArray(): Generator
    {
        foreach ($this->sorts as $sort) {
            yield from $sort->toArray();
        }
    }
}
