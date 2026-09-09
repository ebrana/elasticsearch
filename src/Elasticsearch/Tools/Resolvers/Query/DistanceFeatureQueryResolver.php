<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class DistanceFeatureQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        foreach (['field', 'origin', 'pivot'] as $required) {
            if (!isset($metadata[$required])) {
                throw new RuntimeException(sprintf('Distance feature query must have %s property.', $required));
            }
        }

        $property ??= '$distanceFeatureQuery';
        $arguments = [
            sprintf('field: %s', $this->resolveValue($metadata['field'])),
            sprintf('origin: %s', $this->resolveValue($metadata['origin'])),
            sprintf('pivot: %s', $this->resolveValue($metadata['pivot'])),
        ];

        if (isset($metadata['boost'])) {
            $arguments[] = sprintf('boost: %s', $this->resolveValue($metadata['boost']));
        }

        return sprintf('%s = new DistanceFeatureQuery(%s);', $property, implode(', ', $arguments));
    }
}
