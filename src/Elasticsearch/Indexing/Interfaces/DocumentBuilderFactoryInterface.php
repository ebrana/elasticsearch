<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Interfaces;

use Elasticsearch\Mapping\Index;

interface DocumentBuilderFactoryInterface
{
    public function create(Index $index): DocumentBuilderInterface;
    public static function getEntityClass(): string;
}