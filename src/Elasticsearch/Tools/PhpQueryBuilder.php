<?php

declare(strict_types=1);

namespace Elasticsearch\Tools;

use Elasticsearch\Tools\Resolvers\AggregationResolver;
use Elasticsearch\Tools\Resolvers\CollapseResolver;
use Elasticsearch\Tools\Resolvers\SortResolver;
use Elasticsearch\Tools\Resolvers\Query\QueryResolver;

readonly final class PhpQueryBuilder
{
    private QueryResolver $queryResolver;
    private AggregationResolver $aggregationResolver;
    private SortResolver $sortResolver;
    private CollapseResolver $collapseResolver;

    public function __construct()
    {
        $this->queryResolver = new QueryResolver();
        $this->sortResolver = new SortResolver($this->queryResolver);
        $this->aggregationResolver = new AggregationResolver($this->queryResolver, $this->sortResolver);
        $this->collapseResolver = new CollapseResolver();
    }

    /**
     * @throws \JsonException
     */
    public function fromJson(string $json): string
    {
        $encoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $this->fromArray($encoded);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function fromArray(array $query): string
    {
        $body = isset($query['body']) && is_array($query['body']) ? $query['body'] : $query;
        $hasBodyParts = $this->hasBodyParts($body);
        $lines = [];

        if (isset($body['query']) && is_array($body['query'])) {
            $property = $hasBodyParts ? '$query' : null;
            $resolved = $this->queryResolver->resolve($body['query'], $property);
            if ('' !== $resolved) {
                $lines[] = $resolved;
            }

            if ($hasBodyParts) {
                $lines[] = '$builder->setQuery($query);';
            }
        }

        if (!$hasBodyParts) {
            if ([] !== $lines) {
                return implode(PHP_EOL, $lines);
            }

            return $this->queryResolver->resolve($body);
        }

        if (isset($body['aggs']) && is_array($body['aggs'])) {
            $this->appendLines($lines, $this->aggregationResolver->resolve($body['aggs']));
        }

        if (isset($body['sort']) && is_array($body['sort'])) {
            $this->appendLines($lines, $this->sortResolver->resolve($body['sort']));
        }

        if (isset($body['collapse']) && is_array($body['collapse'])) {
            $this->appendLines($lines, $this->collapseResolver->resolve($body['collapse']));
        }

        if (isset($body['size'])) {
            $lines[] = sprintf('$builder->size(%d);', (int) $body['size']);
        }

        if (isset($body['from'])) {
            $lines[] = sprintf('$builder->from(%d);', (int) $body['from']);
        }

        if (isset($body['search_after']) && is_array($body['search_after'])) {
            $lines[] = sprintf('$builder->searchAfter(%s);', $this->resolveArrayLiteral($body['search_after']));
        }

        if (isset($body['_source'])) {
            $source = $body['_source'];
            if (is_array($source) && array_is_list($source)) {
                $lines[] = sprintf('$builder->fields(%s);', $this->resolveArrayLiteral($source));
            } elseif (is_array($source) && isset($source['includes']) && is_array($source['includes'])) {
                $lines[] = sprintf('$builder->fields(%s);', $this->resolveArrayLiteral($source['includes']));
            }
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function hasBodyParts(array $body): bool
    {
        return isset($body['aggs'])
            || isset($body['sort'])
            || isset($body['collapse'])
            || isset($body['size'])
            || isset($body['from'])
            || isset($body['search_after'])
            || isset($body['_source']);
    }

    /**
     * @param array<int|string, mixed> $values
     */
    private function resolveArrayLiteral(array $values): string
    {
        $parts = [];
        foreach ($values as $key => $value) {
            $valueLiteral = $this->resolveScalarLiteral($value);
            if (is_int($key)) {
                $parts[] = $valueLiteral;
            } else {
                $parts[] = sprintf("'%s' => %s", addslashes((string) $key), $valueLiteral);
            }
        }

        return '[' . implode(', ', $parts) . ']';
    }

    private function resolveScalarLiteral(mixed $value): string
    {
        if (is_string($value)) {
            return sprintf("'%s'", addslashes($value));
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_array($value)) {
            return $this->resolveArrayLiteral($value);
        }

        return 'null';
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
