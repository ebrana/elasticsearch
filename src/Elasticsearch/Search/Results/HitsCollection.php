<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

use ArrayIterator;
use Countable;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, scalar|null>
 */
final class HitsCollection implements IteratorAggregate, Countable
{
    private ArrayCollection $collection;
    private ?float $max_score = null;
    private int $totalValue = 0;
    private ?string $totalRelation = null;

    public function __construct(?ArrayCollection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }

    public function setCollection(ArrayCollection $collection): void
    {
        $this->collection = $collection;
    }

    public function getMaxScore(): ?float
    {
        return $this->max_score;
    }

    public function setMaxScore(?float $max_score): void
    {
        $this->max_score = $max_score;
    }

    public function getTotalValue(): int
    {
        return $this->totalValue;
    }

    public function setTotalValue(int $totalValue): void
    {
        $this->totalValue = $totalValue;
    }

    public function getTotalRelation(): ?string
    {
        return $this->totalRelation;
    }

    public function setTotalRelation(?string $totalRelation): void
    {
        $this->totalRelation = $totalRelation;
    }

    public function getIterator(): ArrayIterator
    {
        /** <array{_index: string, _id: string, _score: int|null, _source: array|null, sort: array}>  */
        /** @var ArrayIterator<int, scalar|null> $iterator */
        $iterator = $this->collection->getIterator();

        return $iterator;
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    /**
     * @return array<int, scalar|null>
     */
    public function toArray(): array
    {
        return $this->collection->toArray();
    }
}
