<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;

/**
 * Zaindexuje dokument jen kdyz s timto _id jeste neexistuje - jinak polozka selze
 * se statusem 409 a chyba je v BulkResponse::getErrors().
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
