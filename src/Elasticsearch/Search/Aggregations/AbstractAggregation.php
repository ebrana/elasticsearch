<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;
use Generator;

abstract class AbstractAggregation
{
    protected string $name = '';

    /** @var array<string, string> */
    protected array $meta = [];

    abstract public function payload(): ArrayCollection;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param array<string, string> $meta
     * @return $this
     */
    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function toArray(): Generator
    {
        $payload = iterator_to_array($this->payload());

        if (count($this->meta) > 0) {
            $payload['meta'] = $this->meta;
        }

        yield $payload;
    }
}
