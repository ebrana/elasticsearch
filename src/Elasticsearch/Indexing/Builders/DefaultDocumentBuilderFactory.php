<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Builders;

use Elasticsearch\Indexing\Interfaces\DocumentBuilderFactoryInterface;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderInterface;
use Elasticsearch\Mapping\Index;

class DefaultDocumentBuilderFactory implements DocumentBuilderFactoryInterface
{
    public const DEFAULT = '~default~';

    public function create(Index $index): DocumentBuilderInterface
    {
        return new DefaultDocumentBuilder($index);
    }

    public static function getEntityClass(): string
    {
        return self::DEFAULT;
    }
}
