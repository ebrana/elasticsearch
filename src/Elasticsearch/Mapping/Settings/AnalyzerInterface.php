<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

interface AnalyzerInterface
{
    public function getName(): string;

    public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
