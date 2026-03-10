<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class NestedQueryResolver extends AbstractQueryResolver
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['path'])) {
            throw new \RuntimeException('Nested query must have path property.');
        }
        if (!isset($metadata['query'])) {
            throw new \RuntimeException('Nested query must have query property.');
        }

        $path = $metadata['path'];
        $property = $property ?? '$nestedQuery';
        $result = '';
        $query = $metadata['query'];

        $result .= $this->queryResolver->resolve($query, '$subQuery');
        $result .= PHP_EOL;
        $result .= sprintf('%s = new NestedQuery(\'%s\', %s);', $property, $path, '$subQuery');

        return $result;
    }
}
