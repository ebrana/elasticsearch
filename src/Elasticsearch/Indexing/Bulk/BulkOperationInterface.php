<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Mapping\Index;

/**
 * A single operation in a bulk request. The index name (including the prefix) is filled in by
 * Connection, because only it knows the prefix.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-bulk.html
 */
interface BulkOperationInterface
{
    /**
     * index, create, update nebo delete
     */
    public function getAction(): string;

    public function getIndex(): Index;

    public function getId(): ?string;

    /**
     * Further keys for the metadata row, e.g. routing or if_seq_no.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    /**
     * Data row below the metadata. Delete has none, hence null.
     *
     * @return array<string, mixed>|null
     */
    public function getSource(): ?array;
}
