<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations;

use Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = $this->payload()->toArray();

        if (count($this->meta) > 0) {
            $payload['meta'] = $this->meta;
        }

        return $payload;
    }
}
