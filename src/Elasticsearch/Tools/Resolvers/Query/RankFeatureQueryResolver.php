<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class RankFeatureQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['field'])) {
            throw new RuntimeException('Rank feature query must have field property.');
        }

        $property ??= '$rankFeatureQuery';
        $arguments = [sprintf('field: %s', $this->resolveValue($metadata['field']))];

        $function = $this->resolveFunction($metadata);
        if (null !== $function) {
            $arguments[] = sprintf('function: %s', $function);
        }

        if (isset($metadata['boost'])) {
            $arguments[] = sprintf('boost: %s', $this->resolveValue($metadata['boost']));
        }

        return sprintf('%s = new RankFeatureQuery(%s);', $property, implode(', ', $arguments));
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function resolveFunction(array $metadata): ?string
    {
        if (isset($metadata['saturation'])) {
            $pivot = is_array($metadata['saturation']) ? ($metadata['saturation']['pivot'] ?? null) : null;

            return null === $pivot
                ? 'new SaturationFunction()'
                : sprintf('new SaturationFunction(%s)', $this->resolveValue($pivot));
        }

        if (isset($metadata['log']['scaling_factor'])) {
            return sprintf('new LogarithmFunction(%s)', $this->resolveValue($metadata['log']['scaling_factor']));
        }

        if (isset($metadata['sigmoid']['pivot'], $metadata['sigmoid']['exponent'])) {
            return sprintf(
                'new SigmoidFunction(%s, %s)',
                $this->resolveValue($metadata['sigmoid']['pivot']),
                $this->resolveValue($metadata['sigmoid']['exponent'])
            );
        }

        if (isset($metadata['linear'])) {
            return 'new LinearFunction()';
        }

        return null;
    }
}
