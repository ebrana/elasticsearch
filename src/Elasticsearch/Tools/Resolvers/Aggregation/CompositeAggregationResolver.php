<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Aggregation;

class CompositeAggregationResolver extends AbstractAggregationResolver
{
    use MetricAggregationResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     * @return string[]
     */
    public function resolve(string $name, array $metadata, string $property): array
    {
        $sources = [];

        if (isset($metadata['sources']) && is_array($metadata['sources'])) {
            foreach ($metadata['sources'] as $source) {
                if (!is_array($source)) {
                    continue;
                }

                $sourceName = (string)array_key_first($source);
                $definition = $source[$sourceName] ?? null;
                if (!is_array($definition)) {
                    continue;
                }

                $type = (string)array_key_first($definition);
                /** @var array<string, mixed> $options */
                $options = is_array($definition[$type] ?? null) ? $definition[$type] : [];
                $resolved = $this->resolveSource($type, $sourceName, $options);

                if (null !== $resolved) {
                    $sources[] = $resolved;
                }
            }
        }

        $lines = [
            sprintf(
                '%s = new CompositeAggregation(%s%s);',
                $property,
                $this->resolvePhpValue($name),
                $sources ? ', ' . implode(', ', $sources) : ''
            ),
        ];

        return array_merge($lines, $this->resolveOptions($metadata, $property, [
            'size'  => 'size',
            'after' => 'after',
        ]));
    }

    /**
     * @param array<string, mixed> $options
     */
    private function resolveSource(string $type, string $name, array $options): ?string
    {
        $field = $this->resolvePhpValue($options['field'] ?? '');

        return match ($type) {
            'terms' => sprintf('new TermsSource(%s, %s)', $this->resolvePhpValue($name), $field),
            'histogram' => sprintf(
                'new HistogramSource(%s, %s, %s)',
                $this->resolvePhpValue($name),
                $field,
                $this->resolvePhpValue($options['interval'] ?? 0)
            ),
            'date_histogram' => sprintf(
                'new DateHistogramSource(%s, %s, %s, %s)',
                $this->resolvePhpValue($name),
                $field,
                $this->resolvePhpValue(
                    $options['calendar_interval'] ?? $options['fixed_interval'] ?? ''
                ),
                isset($options['calendar_interval']) ? 'true' : 'false'
            ),
            default => null,
        };
    }
}
