<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

use Elasticsearch\Tools\Resolvers\PhpValueResolverTrait;

trait ValueResolverTrait
{
    use PhpValueResolverTrait;

    private function resolveValue(mixed $value): string
    {
        return $this->resolvePhpValue($value);
    }
}
