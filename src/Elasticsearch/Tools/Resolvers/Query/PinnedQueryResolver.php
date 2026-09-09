<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class PinnedQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['organic']) || !is_array($metadata['organic'])) {
            throw new RuntimeException('Pinned query must have organic property.');
        }
        if (!isset($metadata['ids']) && !isset($metadata['docs'])) {
            throw new RuntimeException('Pinned query must have ids or docs property.');
        }

        $property ??= '$pinnedQuery';
        $result = $this->queryResolver->resolve($metadata['organic'], '$pinnedOrganic') . PHP_EOL;

        $arguments = ['organic: $pinnedOrganic'];
        if (isset($metadata['ids'])) {
            $arguments[] = sprintf('ids: %s', $this->resolveValue($metadata['ids']));
        } else {
            $arguments[] = sprintf('docs: %s', $this->resolveValue($metadata['docs']));
        }

        return $result . sprintf('%s = new PinnedQuery(%s);', $property, implode(', ', $arguments));
    }
}
