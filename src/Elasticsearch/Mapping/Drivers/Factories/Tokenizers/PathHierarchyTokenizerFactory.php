<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Tokenizers;

use Elasticsearch\Mapping\Settings\Tokenizers\PathHierarchyTokenizer;
use stdClass;

class PathHierarchyTokenizerFactory implements TokenizerFactoryInterface
{
    /**
     * @param stdClass&object{
     *     delimiter?: string,
     *     replacement?: string,
     *     buffer_size?: int,
     *     reverse?: bool,
     *     skip?: int
     * } $configuration
     */
    public static function create(string $name, stdClass $configuration): PathHierarchyTokenizer
    {
        return new PathHierarchyTokenizer(
            $name,
            $configuration->delimiter ?? PathHierarchyTokenizer::DEFAULT_DELIMITER,
            $configuration->replacement ?? null,
            (int)($configuration->buffer_size ?? PathHierarchyTokenizer::DEFAULT_BUFFER_SIZE),
            (bool)($configuration->reverse ?? false),
            (int)($configuration->skip ?? PathHierarchyTokenizer::DEFAULT_SKIP)
        );
    }
}
