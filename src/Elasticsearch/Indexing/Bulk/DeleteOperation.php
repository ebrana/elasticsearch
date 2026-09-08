<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Mapping\Index;

/**
 * Deletes a document by _id. Has no data row.
 */
readonly class DeleteOperation implements BulkOperationInterface
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private Index $index,
        private string $id,
        private array $metadata = [],
    ) {
    }

    public function getAction(): string
    {
        return 'delete';
    }

    public function getIndex(): Index
    {
        return $this->index;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getSource(): ?array
    {
        return null;
    }
}
