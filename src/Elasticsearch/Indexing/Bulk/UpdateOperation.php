<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Elasticsearch\Mapping\Index;
use RuntimeException;

/**
 * Castecna zmena dokumentu - bud predanymi polemi (`doc`), nebo skriptem (`script`).
 * `docAsUpsert` zaridi, ze se dokument vytvori, kdyz jeste neexistuje.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-update.html
 */
readonly class UpdateOperation implements BulkOperationInterface
{
    /**
     * @param array<string, mixed>|null $doc
     * @param array<string, mixed>|null $script
     * @param array<string, mixed>|null $upsert dokument pouzity, kdyz cil neexistuje
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private Index $index,
        private string $id,
        private ?array $doc = null,
        private ?array $script = null,
        private bool $docAsUpsert = false,
        private ?array $upsert = null,
        private ?int $retryOnConflict = null,
        private array $metadata = [],
    ) {
    }

    public function getAction(): string
    {
        return 'update';
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
        $metadata = $this->metadata;

        if (null !== $this->retryOnConflict) {
            $metadata['retry_on_conflict'] = $this->retryOnConflict;
        }

        return $metadata;
    }

    public function getSource(): ?array
    {
        if (null === $this->doc && null === $this->script) {
            throw new RuntimeException('Update operation must define doc or script.');
        }

        if (null !== $this->doc && null !== $this->script) {
            throw new RuntimeException('Update operation accepts either doc or script, not both.');
        }

        $source = null !== $this->doc ? ['doc' => $this->doc] : ['script' => (array)$this->script];

        if ($this->docAsUpsert) {
            $source['doc_as_upsert'] = true;
        }

        if (null !== $this->upsert) {
            $source['upsert'] = $this->upsert;
        }

        return $source;
    }
}
