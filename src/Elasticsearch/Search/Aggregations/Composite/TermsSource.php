<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

class TermsSource extends AbstractCompositeSource
{
    protected function getType(): string
    {
        return 'terms';
    }

    protected function provideSource(): array
    {
        return [];
    }
}
