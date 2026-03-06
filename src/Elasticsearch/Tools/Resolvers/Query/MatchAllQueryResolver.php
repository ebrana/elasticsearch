<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class MatchAllQueryResolver extends AbstractQueryResolver
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $property = $property ?? '$matchAllQuery';

        return sprintf('%s = new MatchAllQuery();', $property);
    }
}
