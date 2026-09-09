<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class BoostingQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        foreach (['positive', 'negative', 'negative_boost'] as $required) {
            if (!isset($metadata[$required])) {
                throw new RuntimeException(sprintf('Boosting query must have %s property.', $required));
            }
        }

        if (!is_array($metadata['positive']) || !is_array($metadata['negative'])) {
            throw new RuntimeException('Boosting query positive and negative must be queries.');
        }

        $property ??= '$boostingQuery';
        $result = $this->queryResolver->resolve($metadata['positive'], '$boostingPositive') . PHP_EOL;
        $result .= $this->queryResolver->resolve($metadata['negative'], '$boostingNegative') . PHP_EOL;

        $arguments = [
            'positive: $boostingPositive',
            'negative: $boostingNegative',
            sprintf('negative_boost: %s', $this->resolveValue($metadata['negative_boost'])),
        ];
        if (isset($metadata['boost'])) {
            $arguments[] = sprintf('boost: %s', $this->resolveValue($metadata['boost']));
        }

        return $result . sprintf('%s = new BoostingQuery(%s);', $property, implode(', ', $arguments));
    }
}
