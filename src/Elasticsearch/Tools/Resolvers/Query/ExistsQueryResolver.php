<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class ExistsQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = $metadata['field'] ?? null;
        if (!is_string($field) || $field === '') {
            throw new \RuntimeException('Exists query must contain field.');
        }

        $property = $property ?? '$existsQuery';

        return sprintf('%s = new ExistsQuery(%s);', $property, $this->resolveValue($field));
    }
}
