<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;

/**
 * Zaindexuje dokument; pokud uz s timto _id existuje, prepise ho.
 */
readonly class IndexOperation implements BulkOperationInterface
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private DocumentInterface $document,
        private array $metadata = [],
    ) {
    }

    public function getAction(): string
    {
        return 'index';
    }

    public function getIndex(): Index
    {
        return $this->document->getIndex();
    }

    public function getId(): ?string
    {
        return $this->document->getId();
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getSource(): ?array
    {
        return $this->document->toArray();
    }
}
