<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class FiltersAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $lines = [sprintf('%s = new FiltersAggregation(%s);', $property, $this->resolvePhpValue($name))];

        if (isset($metadata['filters']) && is_array($metadata['filters'])) {
            $index = 0;
            foreach ($metadata['filters'] as $key => $filter) {
                if (!is_array($filter)) {
                    continue;
                }

                $filterProperty = sprintf('%sFilter%d', $property, $index++);
                $lines[] = $this->aggregationResolver->resolveQuery($filter, $filterProperty);
                $lines[] = sprintf(
                    '%s->filter(%s, %s);',
                    $property,
                    $this->resolvePhpValue((string)$key),
                    $filterProperty
                );
            }
        }

        if (isset($metadata['other_bucket'])) {
            $lines[] = sprintf(
                '%s->otherBucket(%s%s);',
                $property,
                $metadata['other_bucket'] ? 'true' : 'false',
                isset($metadata['other_bucket_key'])
                    ? ', ' . $this->resolvePhpValue($metadata['other_bucket_key'])
                    : ''
            );
        }

        return $lines;
    }
}
