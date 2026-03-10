<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class DisMaxQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $property = $property ?? '$disMaxQuery';
        $result = sprintf('%s = new DisMaxQuery(queries: []', $property);

        if (isset($metadata['tie_breaker'])) {
            $result .= sprintf(', tie_breaker: %s', $this->resolveValue((float) $metadata['tie_breaker']));
        }

        $result .= ');';

        if (!isset($metadata['queries']) || !is_array($metadata['queries'])) {
            throw new \RuntimeException('Dis max query must contain queries list.');
        }

        foreach ($metadata['queries'] as $index => $query) {
            if (!is_array($query)) {
                continue;
            }

            $subProperty = sprintf('$disMaxQuery%s', $index);
            $resolved = $this->queryResolver->resolve($query, $subProperty);
            if ($resolved === '') {
                continue;
            }

            $result .= PHP_EOL . $resolved;
            $result .= PHP_EOL . sprintf('%s->addQuery(%s);', $property, $subProperty);
        }

        return $result;
    }
}
