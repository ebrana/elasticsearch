<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

/**
 * Error of a single bulk request item. Bulk returns HTTP 200 even when some of the items fail,
 * so without checking BulkResponse::hasErrors() the errors would pass unnoticed.
 */
final readonly class BulkItemError
{
    public function __construct(
        private string $action,
        private string $index,
        private ?string $id,
        private int $status,
        private ?string $type,
        private ?string $reason,
    ) {
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s/%s failed with %d: %s (%s)',
            $this->action,
            $this->index,
            $this->id ?? '-',
            $this->status,
            $this->reason ?? 'unknown reason',
            $this->type ?? 'unknown type'
        );
    }
}
