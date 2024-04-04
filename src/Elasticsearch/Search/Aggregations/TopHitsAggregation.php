<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Sorts\Sort;

class TopHitsAggregation extends AbstractAggregation
{
    public function __construct(
        string $name,
        private readonly int $size,
        private readonly ?Sort $sort = null
    ) {
        $this->name = $name;
    }

    public function payload(): ArrayCollection
    {
        $parameters = [
            'size' => $this->size,
        ];

        if ($this->sort) {
            $parameters['sort'] = [$this->sort->toArray()->current()];
        }

        return new ArrayCollection(['top_hits' => $parameters]);
    }
}
