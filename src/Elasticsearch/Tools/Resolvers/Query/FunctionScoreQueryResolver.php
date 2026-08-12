<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use RuntimeException;

class FunctionScoreQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;

    /** Klice, ktere na urovni function_score nejsou funkce. */
    private const array RESERVED_KEYS = [
        'query',
        'functions',
        'score_mode',
        'boost_mode',
        'max_boost',
        'min_score',
        'boost',
    ];

    private const array DECAY_TYPES = ['gauss', 'exp', 'linear'];

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        if (!isset($metadata['query']) || !is_array($metadata['query'])) {
            throw new RuntimeException('Function score query must have query property.');
        }

        $property ??= '$functionScoreQuery';
        $lines = [$this->queryResolver->resolve($metadata['query'], '$functionScoreInner')];

        $functionVariables = [];
        foreach ($this->collectFunctions($metadata) as $index => $function) {
            $variable = sprintf('$scoreFunction%d', $index);
            $lines[] = $this->resolveFunction($function, $variable);
            $functionVariables[] = $variable;
        }

        $arguments = ['query: $functionScoreInner'];
        if ($functionVariables) {
            $arguments[] = sprintf('functions: [%s]', implode(', ', $functionVariables));
        }

        foreach (['score_mode' => 'ScoreMode', 'boost_mode' => 'BoostMode'] as $key => $enum) {
            if (isset($metadata[$key])) {
                $arguments[] = sprintf('%s: %s::%s', $key, $enum, strtoupper((string)$metadata[$key]));
            }
        }

        foreach (['max_boost', 'min_score', 'boost'] as $key) {
            if (isset($metadata[$key])) {
                $arguments[] = sprintf('%s: %s', $key, $this->resolveValue($metadata[$key]));
            }
        }

        $lines[] = sprintf('%s = new FunctionScoreQuery(%s);', $property, implode(', ', $arguments));

        return implode(PHP_EOL, array_filter($lines, static fn (string $line): bool => '' !== $line));
    }

    /**
     * Elasticsearch pripousti jak `functions: [...]`, tak jednu funkci zapsanou primo
     * na urovni function_score.
     *
     * @param array<string, mixed> $metadata
     * @return array<int, array<string, mixed>>
     */
    private function collectFunctions(array $metadata): array
    {
        if (isset($metadata['functions']) && is_array($metadata['functions'])) {
            /** @var array<int, array<string, mixed>> $functions */
            $functions = array_values(array_filter($metadata['functions'], 'is_array'));

            return $functions;
        }

        $inline = array_diff_key($metadata, array_flip(self::RESERVED_KEYS));

        return [] === $inline ? [] : [$inline];
    }

    /**
     * @param array<string, mixed> $function
     */
    private function resolveFunction(array $function, string $variable): string
    {
        $arguments = $this->resolveFunctionArguments($function);
        if (null === $arguments) {
            throw new RuntimeException(
                sprintf('Unsupported function score function: %s.', implode(', ', array_keys($function)))
            );
        }

        [$class, $functionArguments, $filterLine] = $arguments;

        if (isset($function['filter']) && is_array($function['filter'])) {
            $filterVariable = $variable . 'Filter';
            $filterLine = $this->queryResolver->resolve($function['filter'], $filterVariable) . PHP_EOL;
            $functionArguments[] = sprintf('filter: %s', $filterVariable);
        }

        if (isset($function['weight']) && 'WeightFunction' !== $class) {
            $functionArguments[] = sprintf('weight: %s', $this->resolveValue($function['weight']));
        }

        return $filterLine . sprintf('%s = new %s(%s);', $variable, $class, implode(', ', $functionArguments));
    }

    /**
     * @param array<string, mixed> $function
     * @return array{0: string, 1: string[], 2: string}|null
     */
    private function resolveFunctionArguments(array $function): ?array
    {
        if (isset($function['field_value_factor']) && is_array($function['field_value_factor'])) {
            $options = $function['field_value_factor'];
            $arguments = [sprintf('field: %s', $this->resolveValue($options['field'] ?? null))];

            if (isset($options['factor'])) {
                $arguments[] = sprintf('factor: %s', $this->resolveValue($options['factor']));
            }
            if (isset($options['modifier'])) {
                $arguments[] = sprintf(
                    'modifier: FieldValueFactorModifier::%s',
                    strtoupper((string)$options['modifier'])
                );
            }
            if (isset($options['missing'])) {
                $arguments[] = sprintf('missing: %s', $this->resolveValue($options['missing']));
            }

            return ['FieldValueFactorFunction', $arguments, ''];
        }

        if (array_key_exists('random_score', $function)) {
            $options = is_array($function['random_score']) ? $function['random_score'] : [];
            $arguments = [];

            if (isset($options['seed'])) {
                $arguments[] = sprintf('seed: %s', $this->resolveValue($options['seed']));
            }
            if (isset($options['field'])) {
                $arguments[] = sprintf('field: %s', $this->resolveValue($options['field']));
            }

            return ['RandomScoreFunction', $arguments, ''];
        }

        if (isset($function['script_score']['script'])) {
            return [
                'ScriptScoreFunction',
                [sprintf('script: %s', $this->resolveValue($function['script_score']['script']))],
                '',
            ];
        }

        foreach (self::DECAY_TYPES as $type) {
            if (isset($function[$type]) && is_array($function[$type])) {
                return $this->resolveDecayFunction($type, $function[$type]);
            }
        }

        if (isset($function['weight'])) {
            return ['WeightFunction', [sprintf('weight: %s', $this->resolveValue($function['weight']))], ''];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $options
     * @return array{0: string, 1: string[], 2: string}
     */
    private function resolveDecayFunction(string $type, array $options): array
    {
        $class = match ($type) {
            'gauss'  => 'GaussDecayFunction',
            'exp'    => 'ExpDecayFunction',
            default  => 'LinearDecayFunction',
        };

        $multiValueMode = $options['multi_value_mode'] ?? null;
        unset($options['multi_value_mode']);

        $field = (string)array_key_first($options);
        /** @var array<string, mixed> $parameters */
        $parameters = is_array($options[$field] ?? null) ? $options[$field] : [];

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('origin: %s', $this->resolveValue($parameters['origin'] ?? null)),
            sprintf('scale: %s', $this->resolveValue($parameters['scale'] ?? null)),
        ];

        if (isset($parameters['offset'])) {
            $arguments[] = sprintf('offset: %s', $this->resolveValue($parameters['offset']));
        }
        if (isset($parameters['decay'])) {
            $arguments[] = sprintf('decay: %s', $this->resolveValue($parameters['decay']));
        }
        if (null !== $multiValueMode) {
            $arguments[] = sprintf('multi_value_mode: MultiValueMode::%s', strtoupper((string)$multiValueMode));
        }

        return [$class, $arguments, ''];
    }
}
