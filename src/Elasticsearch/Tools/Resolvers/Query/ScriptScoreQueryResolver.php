<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class ScriptScoreQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['query']) || !is_array($metadata['query'])) {
            throw new RuntimeException('Script score query must have query property.');
        }
        if (!isset($metadata['script'])) {
            throw new RuntimeException('Script score query must have script property.');
        }

        $property ??= '$scriptScoreQuery';
        $result = $this->queryResolver->resolve($metadata['query'], '$scriptScoreInner') . PHP_EOL;

        $arguments = [
            'query: $scriptScoreInner',
            sprintf('script: %s', $this->resolveValue($metadata['script'])),
        ];

        foreach (['min_score', 'boost'] as $key) {
            if (isset($metadata[$key])) {
                $arguments[] = sprintf('%s: %s', $key, $this->resolveValue($metadata[$key]));
            }
        }

        return $result . sprintf('%s = new ScriptScoreQuery(%s);', $property, implode(', ', $arguments));
    }
}
