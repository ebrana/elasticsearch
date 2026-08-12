<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Analyze;

final readonly class AnalyzeToken
{
    public function __construct(
        private string $token,
        private int $startOffset,
        private int $endOffset,
        private string $type,
        private int $position,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    public function getEndOffset(): int
    {
        return $this->endOffset;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
