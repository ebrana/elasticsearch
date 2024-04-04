<?php

declare(strict_types=1);

namespace Elasticsearch\Search;

use Elasticsearch\Mapping\MetadataProviderInterface;
use Elasticsearch\Search\Exceptions\MissingMappingMetadataIndexException;

readonly class SearchBuilderFactory
{
    public function __construct(
        private MetadataProviderInterface $metadataProvider,
        private ?string $indexPrefix = null
    ) {
    }

    public function create(string $class): Builder
    {
        $index = $this->metadataProvider->getMappingMetadata()->getIndexByClasss($class);
        if (null === $index) {
            throw new MissingMappingMetadataIndexException($class);
        }

        $builder = new Builder($index);
        $builder->setIndexPrefix($this->indexPrefix);

        return $builder;
    }
}
