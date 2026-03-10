<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->innerHits as $innerHit) {
            $result[] = $innerHit->toArray();
        }

        return $result;
    }
}
