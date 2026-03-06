<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class QueryResolver
{
    /** @var class-string[]  */
    private array $types = [
        'term' => TermQueryResolver::class,
        'terms' => TermsQueryResolver::class,
        'wildcard' => WildcardQueryResolver::class,
        'range' => RangeQueryResolver::class,
        'prefix' => PrefixQueryResolver::class,
        'exists' => ExistsQueryResolver::class,
        'match_all' => MatchAllQueryResolver::class,
        'match' => MatchQueryResolver::class,
        'multi_match' => MultiMatchQueryResolver::class,
        'query_string' => QueryStringQueryResolver::class,
        'dis_max' => DisMaxQueryResolver::class,
        'bool' => BoolQueryResolver::class,
        'nested' => NestedQueryResolver::class,
    ];

    /**
     * @param array<string, mixed> $query
     */
    public function resolve(array $query, ?string $property = null): string
    {
        $result = '';

        foreach ($query as $type => $meta) {
            if (isset($this->types[$type])) {
                /** @var \Elasticsearch\Tools\Resolvers\Query\QueryResolveInterface $queryResolver */
                $queryResolver = new $this->types[$type]($this);
                if ('' !== $result) {
                    $result .= PHP_EOL;
                }

                $result .= $queryResolver->resolve($meta, $property);
            }
        }

        return $result;
    }
}
