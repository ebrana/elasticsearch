<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Sorts\Sort;
use Elasticsearch\Search\SourceTrait;

class TopHitsAggregation extends AbstractAggregation
{
    use SourceTrait;

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
            $parameters['sort'] = [iterator_to_array($this->sort->toArray())];
        }

        if ($this->source) {
            $parameters['_source'] = $this->source;
        }

        return new ArrayCollection(['top_hits' => $parameters]);
    }
}
