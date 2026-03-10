<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class BoolQueryResolver extends AbstractQueryResolver
{
    use BoostTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $property = $property ?? '$boolQuery';
        $lines = [sprintf('%s = new BoolQuery();', $property)];

        $this->appendLines($lines, $this->resolveClause($metadata, 'must', 'must', 'BoolType::MUST', $property));
        $this->appendLines($lines, $this->resolveClause($metadata, 'should', 'should', 'BoolType::SHOULD', $property));
        $this->appendLines($lines, $this->resolveClause($metadata, 'filter', 'filter', 'BoolType::FILTER', $property));
        $this->appendLines($lines, $this->resolveClause($metadata, 'must_not', 'mustNot', 'BoolType::MUST_NOT', $property));

        if (isset($metadata['minimum_should_match'])) {
            $lines[] = sprintf(
                '%s->setMinimumShouldMatch(%s);',
                $property,
                $this->resolveValue($metadata['minimum_should_match'])
            );
        }

        $boost = $this->resolveBoost($metadata);
        if (null !== $boost) {
            $lines[] = sprintf('%s->setBoost(%s);', $property, $boost);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param array<string, mixed> $metadata
     *
     * @return string[]
     */
    private function resolveClause(array $metadata, string $clauseName, string $variablePrefix, string $boolType, string $property): array
    {
        if (!isset($metadata[$clauseName]) || !is_array($metadata[$clauseName])) {
            return [];
        }

        $clause = $metadata[$clauseName];
        $items = array_is_list($clause) ? $clause : [$clause];
        $lines = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemProperty = array_is_list($clause)
                ? sprintf('$%s%s', $variablePrefix, $index)
                : sprintf('$%s', $variablePrefix);

            $resolved = $this->queryResolver->resolve($item, $itemProperty);
            if ('' === $resolved) {
                continue;
            }

            $lines[] = $resolved;
            $lines[] = sprintf('%s->add(%s, %s);', $property, $itemProperty, $boolType);
        }

        return $lines;
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
