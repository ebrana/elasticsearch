<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class RangeQueryResolver extends AbstractQueryResolver
{
    use BoostTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = array_key_first($metadata);
        $property = $property ?? '$rangeQuery';

        if (null === $field || !is_array($metadata[$field])) {
            throw new \RuntimeException('Range query must contain field metadata.');
        }

        $result = sprintf('%s = new RangeQuery(field: %s', $property, $this->resolveValue($field));
        $boost = $this->resolveBoost($metadata[$field]);
        if ($boost) {
            $result .= ', boost: ' . $boost;
        }
        $result .= ');';

        $result .= $this->resolveOperator($metadata[$field], 'gte', $property);
        $result .= $this->resolveOperator($metadata[$field], 'gt', $property);
        $result .= $this->resolveOperator($metadata[$field], 'lte', $property);
        $result .= $this->resolveOperator($metadata[$field], 'lt', $property);

        return $result;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function resolveOperator(array $metadata, string $operator, string $property): string
    {
        if (isset($metadata[$operator])) {
            $result = $property . sprintf('->%s(%s);', $operator, $this->resolveValue($metadata[$operator]));

            return PHP_EOL . $result;
        }

        return '';
    }
}
