<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

trait BoostTrait
{
    /**
     * @param array<string, mixed> $value
     */
    private function resolveBoost(array $value): ?string
    {
        if (isset($value['boost']) && is_numeric($value['boost'])) {
            $formatted = rtrim(rtrim(sprintf('%.12F', (float) $value['boost']), '0'), '.');
            if (!str_contains($formatted, '.')) {
                $formatted .= '.0';
            }

            return $formatted;
        }

        return null;
    }
}
