<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

use Generator;

class InnerHitsCollection
{
    /** @var InnerHits[] */
    protected array $innerHits;

    public function __construct(InnerHits ...$innerHits)
    {
        $this->innerHits = $innerHits;
    }

    public function add(InnerHits $innerHits): self
    {
        $this->innerHits[] = $innerHits;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->aggregations);
    }

    public function toArray(): Generator
    {
        foreach ($this->innerHits as $innerHit) {
            yield $innerHit->toArray()->current();
        }
    }
}
