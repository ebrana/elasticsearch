<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers;

use Elasticsearch\Tools\Resolvers\Aggregation\AbstractAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\AggregationResolveInterface;
use Elasticsearch\Tools\Resolvers\Aggregation\AvgAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\CardinalityAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\ExtendedStatsAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\FilterAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\GlobalAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\MaxAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\MinAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\NestedAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\PercentileRanksAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\PercentilesAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\ReverseNestedAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\StatsAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\SumAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\TermsAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\TopHitsAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\ValueCountAggregationResolver;
use Elasticsearch\Tools\Resolvers\Aggregation\WeightedAvgAggregationResolver;
use Elasticsearch\Tools\Resolvers\Query\QueryResolver;

class AggregationResolver
{
    use PhpValueResolverTrait;

    /** @var array<string, class-string<AggregationResolveInterface>> */
    private array $types = [
        'terms' => TermsAggregationResolver::class,
        'filter' => FilterAggregationResolver::class,
        'nested' => NestedAggregationResolver::class,
        'reverse_nested' => ReverseNestedAggregationResolver::class,
        'global' => GlobalAggregationResolver::class,
        'top_hits' => TopHitsAggregationResolver::class,
        'min' => MinAggregationResolver::class,
        'max' => MaxAggregationResolver::class,
        'sum' => SumAggregationResolver::class,
        'cardinality' => CardinalityAggregationResolver::class,
        'avg' => AvgAggregationResolver::class,
        'value_count' => ValueCountAggregationResolver::class,
        'stats' => StatsAggregationResolver::class,
        'extended_stats' => ExtendedStatsAggregationResolver::class,
        'percentiles' => PercentilesAggregationResolver::class,
        'percentile_ranks' => PercentileRanksAggregationResolver::class,
        'weighted_avg' => WeightedAvgAggregationResolver::class,
    ];

    private int $counter = 0;

    public function __construct(
        private readonly QueryResolver $queryResolver,
        private readonly SortResolver $sortResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $aggregations
     * @return string[]
     */
    public function resolve(array $aggregations, string $target = '$builder'): array
    {
        $lines = [];

        foreach ($aggregations as $name => $definition) {
            if (!is_string($name) || !is_array($definition)) {
                continue;
            }

            [$aggregationLines, $property] = $this->resolveAggregation($name, $definition);
            $this->appendLines($lines, $aggregationLines);
            $lines[] = sprintf('%s->addAggregation(%s);', $target, $property);
        }

        return $lines;
    }

    /**
     * @param array<string, mixed> $definition
     * @return array{0: string[], 1: string}
     */
    public function resolveAggregation(string $name, array $definition, ?string $property = null): array
    {
        $property ??= '$aggregation' . $this->nextId();
        $type = $this->resolveType($definition, $name);
        $payload = $definition[$type];
        if (!is_array($payload)) {
            $payload = [];
        }

        /** @var AbstractAggregationResolver $resolver */
        $resolver = new $this->types[$type]($this);
        $lines = $resolver->resolve($name, $payload, $property);

        if (isset($definition['meta']) && is_array($definition['meta'])) {
            $lines[] = sprintf('%s->meta(%s);', $property, $this->resolvePhpValue($definition['meta']));
        }

        if (isset($definition['aggs']) && is_array($definition['aggs'])) {
            foreach ($definition['aggs'] as $subName => $subDefinition) {
                if (!is_string($subName) || !is_array($subDefinition)) {
                    continue;
                }

                [$subLines, $subProperty] = $this->resolveAggregation($subName, $subDefinition);
                $this->appendLines($lines, $subLines);
                $lines[] = sprintf('%s->aggregation(%s);', $property, $subProperty);
            }
        }

        return [$lines, $property];
    }

    /**
     * @param array<string, mixed> $query
     */
    public function resolveQuery(array $query, string $property): string
    {
        return $this->queryResolver->resolve($query, $property);
    }

    /**
     * @param array<string, mixed> $sort
     * @return array{0: string[], 1: string}
     */
    public function resolveSingleSort(array $sort, string $property): array
    {
        return $this->sortResolver->resolveSingleSort($sort, $property);
    }

    public function nextId(): int
    {
        return ++$this->counter;
    }

    /**
     * @param array<string, mixed> $definition
     */
    private function resolveType(array $definition, string $name): string
    {
        foreach (array_keys($this->types) as $type) {
            if (array_key_exists($type, $definition)) {
                return $type;
            }
        }

        throw new \RuntimeException(sprintf('Unsupported aggregation "%s".', $name));
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
