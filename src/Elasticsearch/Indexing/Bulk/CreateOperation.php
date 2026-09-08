<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;

/**
 * Indexes the document only when no document with this _id exists yet - otherwise the item fails
 * with status 409 and the error is reported in BulkResponse::getErrors().
 */
readonly class CreateOperation implements BulkOperationInterface
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
        return 'create';
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
