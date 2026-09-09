<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class RegexpQueryResolver extends AbstractQueryResolver
{
    use FieldQueryResolverTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        [$field, $options] = $this->resolveFieldMetadata($metadata, 'value', 'Regexp');
        $property ??= '$regexpQuery';

        $arguments = [
            sprintf('field: %s', $this->resolveValue($field)),
            sprintf('value: %s', $this->resolveValue($options['value'])),
        ];

        if (isset($options['flags'])) {
            $flags = array_map(
                static fn (string $flag): string => 'RegexpFlag::' . strtoupper(trim($flag)),
                explode('|', (string)$options['flags'])
            );
            $arguments[] = sprintf('flags: [%s]', implode(', ', $flags));
        }

        $arguments = array_merge(
            $arguments,
            $this->resolveNamedArguments(
                $options,
                ['case_insensitive', 'max_determinized_states', 'rewrite', 'boost']
            )
        );

        return sprintf('%s = new RegexpQuery(%s);', $property, implode(', ', $arguments));
    }
}
