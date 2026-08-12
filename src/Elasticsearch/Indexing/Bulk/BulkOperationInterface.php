<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Mapping\Index;

/**
 * Jedna operace v bulk requestu. Jmeno indexu (vcetne prefixu) doplnuje Connection,
 * protoze prefix zna jen ono.
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
     * Dalsi klice do metadatoveho radku, napr. routing nebo if_seq_no.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    /**
     * Datovy radek pod metadaty. Delete ho nema, proto null.
     *
     * @return array<string, mixed>|null
     */
    public function getSource(): ?array;
}
