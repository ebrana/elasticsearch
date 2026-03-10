<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers\Query;

interface QueryResolveInterface
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function resolve(array $metadata, ?string $property = null): string;
}
