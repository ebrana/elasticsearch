<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class ConstantScoreQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['filter']) || !is_array($metadata['filter'])) {
            throw new RuntimeException('Constant score query must have filter property.');
        }

        $property ??= '$constantScoreQuery';
        $result = $this->queryResolver->resolve($metadata['filter'], '$constantScoreFilter') . PHP_EOL;

        $arguments = ['filter: $constantScoreFilter'];
        if (isset($metadata['boost'])) {
            $arguments[] = sprintf('boost: %s', $this->resolveValue($metadata['boost']));
        }

        return $result . sprintf('%s = new ConstantScoreQuery(%s);', $property, implode(', ', $arguments));
    }
}
