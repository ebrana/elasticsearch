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
        'match_phrase' => MatchPhraseQueryResolver::class,
        'match_phrase_prefix' => MatchPhrasePrefixQueryResolver::class,
        'match_bool_prefix' => MatchBoolPrefixQueryResolver::class,
        'multi_match' => MultiMatchQueryResolver::class,
        'query_string' => QueryStringQueryResolver::class,
        'simple_query_string' => SimpleQueryStringQueryResolver::class,
        'fuzzy' => FuzzyQueryResolver::class,
        'regexp' => RegexpQueryResolver::class,
        'terms_set' => TermsSetQueryResolver::class,
        'ids' => IdsQueryResolver::class,
        'dis_max' => DisMaxQueryResolver::class,
        'bool' => BoolQueryResolver::class,
        'nested' => NestedQueryResolver::class,
        'constant_score' => ConstantScoreQueryResolver::class,
        'function_score' => FunctionScoreQueryResolver::class,
        'boosting' => BoostingQueryResolver::class,
        'script_score' => ScriptScoreQueryResolver::class,
        'pinned' => PinnedQueryResolver::class,
        'distance_feature' => DistanceFeatureQueryResolver::class,
        'rank_feature' => RankFeatureQueryResolver::class,
        'more_like_this' => MoreLikeThisQueryResolver::class,
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
