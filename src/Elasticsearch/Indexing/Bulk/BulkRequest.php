<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Countable;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;

/**
 * A batch of operations for the bulk API. Operations and indices can be mixed within one batch.
 *
 * Elasticsearch recommends batches of roughly thousands of documents or a 5-15 MB body; the size
 * has to be governed by the caller, the library does not split the batch.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
 */
final class BulkRequest implements Countable
{
    /** @var BulkOperationInterface[] */
    private array $operations = [];

    public function __construct(BulkOperationInterface ...$operations)
    {
        foreach ($operations as $operation) {
            $this->add($operation);
        }
    }

    public function add(BulkOperationInterface $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function index(DocumentInterface $document): self
    {
        return $this->add(new IndexOperation($document));
    }

    public function create(DocumentInterface $document): self
    {
        return $this->add(new CreateOperation($document));
    }

    /**
     * @param array<string, mixed> $doc
     */
    public function update(Index $index, string $id, array $doc, bool $docAsUpsert = false): self
    {
        return $this->add(new UpdateOperation($index, $id, doc: $doc, docAsUpsert: $docAsUpsert));
    }

    public function delete(Index $index, string $id): self
    {
        return $this->add(new DeleteOperation($index, $id));
    }

    /**
     * @return BulkOperationInterface[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function isEmpty(): bool
    {
        return [] === $this->operations;
    }

    public function count(): int
    {
        return count($this->operations);
    }
}
