<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Interfaces;

interface DocumentBuilderInterface
{
    public function build(IndexableEntityInterface $entity): DocumentInterface;
}
