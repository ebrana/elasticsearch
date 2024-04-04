<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Interfaces;

use Elasticsearch\Mapping\Index;
use Generator;

interface DocumentInterface
{
    /**
     * @param scalar|array<string|int,scalar|null>|null $value
     */
    public function set(string $key, mixed $value): void;
    public function toArray(): Generator;
    public function toJson(): string;
    public function getIndex(): Index;
    public function getId(): ?string;
}
