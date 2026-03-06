<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

class TermsQueryResolver extends AbstractQueryResolver
{
    use ValueResolverTrait;
    use BoostTrait;

    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string
    {
        $field = null;
        foreach ($metadata as $key => $value) {
            if ($key !== 'boost') {
                $field = $key;
                break;
            }
        }
        if (null === $field) {
            throw new \RuntimeException('Terms query must contain field metadata.');
        }

        $value = $this->resolveValue($metadata[$field]);
        $property = $property ?? '$termsQuery';

        $result = sprintf('%s = new TermsQuery(field: %s, value: %s', $property, $this->resolveValue($field), $value);
        $boost = $this->resolveBoost($metadata);

        if ($boost) {
            $result .= ', boost: ' . $boost;
        }

        $result .= ');';

        return $result;
    }
}
