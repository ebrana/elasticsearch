<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

use Generator;

readonly class InnerHits
{
    /**
     * @param array<string, string>|null $sort
     */
    public function __construct(
        private string $name,
        private int $size,
        private ?int $from = null,
        private ?array $sort = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): Generator
    {
        $data = [
            'name' => $this->name,
            'size' => $this->size,
        ];

        if ($this->from) {
            $data['from'] = $this->from;
        }

        if ($this->sort) {
            $data['sort'] = $this->sort;
        }

        yield $data;
    }
}
