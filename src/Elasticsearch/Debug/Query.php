<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

use Elasticsearch\Search\Results\Result;

class Query
{
    private ?float $start = null;
    private ?float $duration = null;
    private ?string $body = null;
    private Result|null $result = null;
    private ?int $countResult = null;
    private ?bool $boolResult = null;

    public function __construct(
        private readonly string $query,
    ) {
    }

    public function start(): void
    {
        $this->start = microtime(true);
    }

    public function stop(): void
    {
        if (null !== $this->start) {
            $this->duration = microtime(true) - $this->start;
        }
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Query duration in seconds.
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(?Result $result): void
    {
        $this->result = $result;
    }

    public function getCountResult(): ?int
    {
        return $this->countResult;
    }

    public function setCountResult(?int $countResult): void
    {
        $this->countResult = $countResult;
    }

    public function getBoolResult(): ?bool
    {
        return $this->boolResult;
    }

    public function setBoolResult(?bool $boolResult): void
    {
        $this->boolResult = $boolResult;
    }
}
