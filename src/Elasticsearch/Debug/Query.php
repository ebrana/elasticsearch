<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

class Query
{
    private ?float $start = null;
    private ?float $duration = null;
    private ?string $body = null;

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
}
