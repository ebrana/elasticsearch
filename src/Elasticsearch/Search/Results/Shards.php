<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Results;

final readonly class Shards
{
    public function __construct(
        private int $total,
        private int $successful,
        private int $skipped,
        private int $failed
    ) {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getSuccessful(): int
    {
        return $this->successful;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }
}
