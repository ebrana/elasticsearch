<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers;

use Elasticsearch\Tools\Resolvers\Query\QueryResolver;

class SortResolver
{
    use PhpValueResolverTrait;

    private int $counter = 0;

    public function __construct(private readonly QueryResolver $queryResolver)
    {
    }

    /**
     * @param array<string, mixed> $sort
     * @return string[]
     */
    public function resolve(array $sort, string $target = '$builder'): array
    {
        $lines = [];
        foreach ($this->normalizeSortDefinitions($sort) as $definition) {
            if (!is_array($definition)) {
                continue;
            }

            [$definitionLines, $property] = $this->resolveSortDefinition($definition);
            $this->appendLines($lines, $definitionLines);
            $lines[] = sprintf('%s->addSort(%s);', $target, $property);
        }

        return $lines;
    }

    /**
     * @param array<string, mixed> $sort
     * @return array{0: string[], 1: string}
     */
    public function resolveSingleSort(array $sort, ?string $property = null): array
    {
        return $this->resolveSortDefinition($sort, $property);
    }

    /**
     * @param array<string, mixed> $definition
     * @return array{0: string[], 1: string}
     */
    private function resolveSortDefinition(array $definition, ?string $property = null): array
    {
        $property = $property ?? '$sort' . ++$this->counter;
        $field = (string) array_key_first($definition);
        $metadata = $definition[$field] ?? [];

        if ('_script' === $field) {
            return [$this->resolveScriptSort($metadata, $property), $property];
        }

        if ('_geo_distance' === $field) {
            return [$this->resolveGeoDistanceSort($metadata, $property), $property];
        }

        if (!is_array($metadata)) {
            $metadata = ['order' => $metadata];
        }

        $lines = [];
        $order = $this->resolveSortDirection($metadata['order'] ?? null);
        $line = sprintf('%s = new Sort(%s', $property, $this->resolvePhpValue($field));
        if (null !== $order) {
            $line .= sprintf(', %s', $order);
        }
        $line .= ');';
        $lines[] = $line;

        if (isset($metadata['missing'])) {
            $lines[] = sprintf('%s->missing(%s);', $property, $this->resolvePhpValue($metadata['missing']));
        }

        if (isset($metadata['unmapped_type'])) {
            $lines[] = sprintf('%s->unmappedType(%s);', $property, $this->resolvePhpValue($metadata['unmapped_type']));
        }

        if (isset($metadata['mode']) && is_string($metadata['mode'])) {
            $lines[] = sprintf('%s->setMode(%s);', $property, $this->resolveSortMode($metadata['mode']));
        }

        if (isset($metadata['format'])) {
            $lines[] = sprintf('%s->setFormat(%s);', $property, $this->resolvePhpValue($metadata['format']));
        }

        if (isset($metadata['numeric_type'])) {
            $lines[] = sprintf('%s->setNumericType(%s);', $property, $this->resolvePhpValue($metadata['numeric_type']));
        }

        if (isset($metadata['nested']) && is_array($metadata['nested'])) {
            [$nestedLines, $nestedProperty] = $this->resolveNestedSort($metadata['nested']);
            $this->appendLines($lines, $nestedLines);
            $lines[] = sprintf('%s->setNestedSort(%s);', $property, $nestedProperty);
        }

        return [$lines, $property];
    }

    /**
     * @param mixed[] $metadata
     * @return string[]
     */
    private function resolveScriptSort(mixed $metadata, string $property): array
    {
        if (!is_array($metadata)) {
            throw new \RuntimeException('Script sort metadata must be array.');
        }

        $script = $metadata['script'] ?? null;
        if (!is_array($script) || !isset($script['source'])) {
            throw new \RuntimeException('Script sort must contain script.source.');
        }

        $line = sprintf('%s = new ScriptSort(source: %s', $property, $this->resolvePhpValue($script['source']));
        if (isset($script['lang'])) {
            $line .= sprintf(', lang: %s', $this->resolvePhpValue($script['lang']));
        }
        if (isset($script['params']) && is_array($script['params'])) {
            $line .= sprintf(', params: %s', $this->resolvePhpValue($script['params']));
        }
        $order = $this->resolveSortDirection($metadata['order'] ?? null);
        if (null !== $order) {
            $line .= sprintf(', order: %s', $order);
        }
        $line .= ');';

        return [$line];
    }

    /**
     * @param mixed[] $metadata
     * @return string[]
     */
    private function resolveGeoDistanceSort(mixed $metadata, string $property): array
    {
        if (!is_array($metadata)) {
            throw new \RuntimeException('Geo distance sort metadata must be array.');
        }

        $pinLocation = $metadata['pin.location'] ?? null;
        if (null === $pinLocation) {
            throw new \RuntimeException('Geo distance sort currently supports pin.location key.');
        }

        $line = sprintf('%s = new GeoDistanceSort(%s', $property, $this->resolvePhpValue($pinLocation));
        if (isset($metadata['distance_type']) && is_string($metadata['distance_type'])) {
            $line .= sprintf(', distance_type: %s', $this->resolveDistanceType($metadata['distance_type']));
        }
        if (isset($metadata['unit'])) {
            $line .= sprintf(', unit: %s', $this->resolvePhpValue($metadata['unit']));
        }
        $order = $this->resolveSortDirection($metadata['order'] ?? null);
        if (null !== $order) {
            $line .= sprintf(', order: %s', $order);
        }
        if (isset($metadata['mode']) && is_string($metadata['mode'])) {
            $line .= sprintf(', mode: %s', $this->resolveSortMode($metadata['mode']));
        }
        if (isset($metadata['ignore_unmapped'])) {
            $line .= sprintf(', ignore_unmapped: %s', $this->resolvePhpValue((bool) $metadata['ignore_unmapped']));
        }
        $line .= ');';

        return [$line];
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array{0: string[], 1: string}
     */
    private function resolveNestedSort(array $metadata): array
    {
        if (!isset($metadata['path']) || !is_string($metadata['path'])) {
            throw new \RuntimeException('Nested sort must contain path.');
        }

        $property = '$nestedSort' . ++$this->counter;
        $lines = [];
        $filterProperty = null;
        $childProperty = null;

        if (isset($metadata['filter']) && is_array($metadata['filter'])) {
            $filterProperty = '$nestedSortFilter' . $this->counter;
            $resolved = $this->queryResolver->resolve($metadata['filter'], $filterProperty);
            if ('' !== $resolved) {
                $lines[] = $resolved;
            }
        }

        if (isset($metadata['nested']) && is_array($metadata['nested'])) {
            [$childLines, $childProperty] = $this->resolveNestedSort($metadata['nested']);
            $this->appendLines($lines, $childLines);
        }

        $ctor = sprintf('%s = new NestedSort(%s', $property, $this->resolvePhpValue($metadata['path']));
        if (null !== $filterProperty) {
            $ctor .= sprintf(', %s', $filterProperty);
        } elseif (null !== $childProperty) {
            $ctor .= ', null';
        }
        if (null !== $childProperty) {
            $ctor .= sprintf(', %s', $childProperty);
        }
        $ctor .= ');';
        $lines[] = $ctor;

        return [$lines, $property];
    }

    private function resolveSortDirection(mixed $order): ?string
    {
        if (!is_string($order)) {
            return null;
        }

        return strtolower($order) === 'desc' ? 'SortDirection::DESC' : 'SortDirection::ASC';
    }

    private function resolveSortMode(string $mode): string
    {
        return match (strtolower($mode)) {
            'avg' => 'SortMode::AVG',
            'max' => 'SortMode::MAX',
            'min' => 'SortMode::MIN',
            'sum' => 'SortMode::SUM',
            'median' => 'SortMode::MEDIAN',
            default => throw new \RuntimeException(sprintf('Unsupported sort mode "%s".', $mode)),
        };
    }

    private function resolveDistanceType(string $distanceType): string
    {
        return match (strtolower($distanceType)) {
            'plane' => 'DistanceType::PLANE',
            default => 'DistanceType::ARC',
        };
    }

    /**
     * @param array<string, mixed> $sort
     * @return array<int, mixed>
     */
    private function normalizeSortDefinitions(array $sort): array
    {
        if (array_is_list($sort)) {
            return $sort;
        }

        $definitions = [];
        foreach ($sort as $field => $meta) {
            $definitions[] = [$field => $meta];
        }

        return $definitions;
    }

    /**
     * @param string[] $target
     * @param string[] $source
     */
    private function appendLines(array &$target, array $source): void
    {
        foreach ($source as $line) {
            $target[] = $line;
        }
    }
}
