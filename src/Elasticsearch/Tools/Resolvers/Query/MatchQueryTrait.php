<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

trait MatchQueryTrait
{
    use BoostTrait;
    use ValueResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    private function resolveMatch(string $property, array $metadata): string
    {
        $result = $this->resolveOperator($metadata, $property);
        $result .= $this->resolveScalar($metadata, $property, 'boost');
        $result .= $this->resolveScalar($metadata, $property, 'analyzer');
        $result .= $this->resolveScalar($metadata, $property, 'minimum_should_match');
        $result .= $this->resolveScalar($metadata, $property, 'fuzziness');
        $result .= $this->resolveScalar($metadata, $property, 'lenient');
        $result .= $this->resolveScalar($metadata, $property, 'prefix_length');
        $result .= $this->resolveScalar($metadata, $property, 'max_expansions');
        $result .= $this->resolveScalar($metadata, $property, 'zero_terms_query');
        $result .= $this->resolveScalar($metadata, $property, 'fuzzy_rewrite');
        $result .= $this->resolveScalar($metadata, $property, 'auto_generate_synonyms_phrase_query');
        $result .= $this->resolveScalar($metadata, $property, 'fuzzy_transpositions');

        return $result;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function resolveOperator(array $metadata, string $property): string
    {
        $result = '';
        if (isset($metadata['operator'])) {
            $operator = strtoupper((string) $metadata['operator']);
            $result .= PHP_EOL;
            $result .= sprintf('%s->setOperator(%s);',
                $property,
                $operator === 'OR' ? 'Operator::OR' : 'Operator::AND'
            );
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function resolveScalar(array $metadata, string $property, string $field): string
    {
        $setters = [
            'boost' => 'setBoost',
            'analyzer' => 'setAnalyzer',
            'minimum_should_match' => 'setMinimumShouldMatch',
            'fuzziness' => 'setFuzziness',
            'lenient' => 'setLenient',
            'prefix_length' => 'setPrefixLength',
            'max_expansions' => 'setMaxExpansions',
            'zero_terms_query' => 'setZeroTermsQuery',
            'fuzzy_rewrite' => 'setFuzzyRewrite',
            'auto_generate_synonyms_phrase_query' => 'setAutoGenerateSynonymsPhraseQuery',
            'fuzzy_transpositions' => 'setFuzzyTranspositions',
        ];

        $result = '';
        if (isset($metadata[$field])) {
            if (!isset($setters[$field])) {
                throw new \RuntimeException('Unresolver match query setter for field: ' . $field . '.');
            }
            $setter = $setters[$field];
            $result .= PHP_EOL;
            $value = $field === 'boost' ? $this->resolveBoost($metadata) : $this->resolveValue($metadata[$field]);
            $result .= sprintf('%s->%s(%s);', $property, $setter, $value);
        }

        return $result;
    }
}
