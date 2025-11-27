<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Collapse;

use Elasticsearch\Search\SourceTrait;
use Generator;

class InnerHits
{
    use SourceTrait;

    /**
     * @param array<string, string>|null $sort
     */
    public function __construct(
        private readonly string $name,
        private readonly int $size,
        private readonly ?string $collapseField = null,
        private readonly ?int $from = null,
        private readonly ?array $sort = null,
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

        if ($this->collapseField) {
            $data['collapse'] = [
                'field' => $this->collapseField,
            ];
        }

        if ($this->source) {
            $data['_source'] = $this->source;
        }

        yield $data;
    }
}
