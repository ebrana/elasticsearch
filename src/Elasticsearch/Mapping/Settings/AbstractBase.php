<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

abstract class AbstractBase
{
    public function __construct(
        private readonly string $name,
        private readonly string $type
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
        ];
    }
}
