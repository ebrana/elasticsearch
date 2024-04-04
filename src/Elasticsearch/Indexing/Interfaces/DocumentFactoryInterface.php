<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Interfaces;

interface DocumentFactoryInterface
{
    public function create(IndexableEntityInterface $entity): DocumentInterface;
}