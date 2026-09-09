<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

class HistogramSource extends AbstractCompositeSource
{
    public function __construct(
        string $name,
        string $field,
        private readonly float $interval,
    ) {
        parent::__construct($name, $field);
    }

    protected function getType(): string
    {
        return 'histogram';
    }

    protected function provideSource(): array
    {
        return ['interval' => $this->interval];
    }
}
