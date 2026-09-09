<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class IdsQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['values'])) {
            throw new RuntimeException('Ids query must contain values.');
        }

        $property ??= '$idsQuery';
        $arguments = [sprintf('values: %s', $this->resolveValue($metadata['values']))];

        if (isset($metadata['boost'])) {
            $arguments[] = sprintf('boost: %s', $this->resolveValue($metadata['boost']));
        }

        return sprintf('%s = new IdsQuery(%s);', $property, implode(', ', $arguments));
    }
}
