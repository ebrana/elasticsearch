<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class QueryStringQueryResolver extends AbstractQueryResolver
{
    use BoostTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $query = $metadata['query'] ?? null;
        if (!is_string($query) || $query === '') {
            throw new \RuntimeException('Query string query must contain query.');
        }

        $property = $property ?? '$queryStringQuery';
        $lines = [sprintf('%s = new QueryStringQuery(%s);', $property, $this->resolveValue($query))];

        if (isset($metadata['fields']) && is_array($metadata['fields'])) {
            foreach ($metadata['fields'] as $field) {
                if (!is_string($field)) {
                    continue;
                }

                $lines[] = sprintf('%s->field(%s);', $property, $this->resolveValue($field));
            }
        }

        if (isset($metadata['default_operator'])) {
            $operator = strtoupper((string) $metadata['default_operator']);
            $operatorEnum = $operator === 'AND' ? 'Operator::AND' : 'Operator::OR';
            $lines[] = sprintf('%s->setDefaultOperator(%s);', $property, $operatorEnum);
        }

        $boost = $this->resolveBoost($metadata);
        if (null !== $boost) {
            $lines[] = sprintf('%s->setBoost(%s);', $property, $boost);
        }

        $setterMap = [
            'allow_leading_wildcard' => 'setAllowLeadingWildcard',
            'analyze_wildcard' => 'setAnalyzeWildcard',
            'auto_generate_synonyms_phrase_query' => 'setAutoGenerateSynonymsPhraseQuery',
            'enable_position_increments' => 'setEnablePositionIncrements',
            'analyzer' => 'setAnalyzer',
            'fuzziness' => 'setFuzziness',
            'default_field' => 'setDefaultField',
            'fuzzy_max_expansions' => 'setFuzzyMaxExpansions',
            'fuzzy_prefix_length' => 'setFuzzyPrefixLength',
            'fuzzy_transpositions' => 'setFuzzyTranspositions',
            'lenient' => 'setLenient',
            'max_determinized_states' => 'setMaxDeterminizedStates',
            'minimum_should_match' => 'setMinimumShouldMatch',
            'quote_analyzer' => 'setQuoteAnalyzer',
            'phrase_slop' => 'setPhraseSlop',
            'quote_field_suffix' => 'setQuoteFieldSuffix',
            'rewrite' => 'setRewrite',
            'time_zone' => 'setTimeZone',
        ];

        foreach ($setterMap as $field => $setter) {
            if (!array_key_exists($field, $metadata)) {
                continue;
            }

            $lines[] = sprintf('%s->%s(%s);', $property, $setter, $this->resolveValue($metadata[$field]));
        }

        return implode(PHP_EOL, $lines);
    }
}
