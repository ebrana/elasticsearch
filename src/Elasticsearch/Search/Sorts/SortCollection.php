<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

class SortCollection
{
    /** @var SortInterface[] */
    private array $sorts;

    public function __construct(SortInterface ...$sorts)
    {
        $this->sorts = $sorts;
    }

    public function add(SortInterface $sort): self
    {
        $this->sorts[] = $sort;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->sorts);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->sorts as $sort) {
            foreach ($sort->toArray() as $key => $value) {
                $result[(string) $key] = $value;
            }
        }

        return $result;
    }
}
