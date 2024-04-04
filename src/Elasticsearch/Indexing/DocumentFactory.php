<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing;

use Elasticsearch\Indexing\Builders\DefaultDocumentBuilderFactory;
use Elasticsearch\Indexing\Exceptions\NotFoundBuilderFactoryException;
use Elasticsearch\Indexing\Exceptions\NotFoundMetadataIndexException;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderFactoryInterface;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderInterface;
use Elasticsearch\Indexing\Interfaces\DocumentFactoryInterface;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\MetadataProviderInterface;

final class DocumentFactory implements DocumentFactoryInterface
{
    /** @var DocumentBuilderInterface[] */
    private array $builders = [];

    /** @var DocumentBuilderFactoryInterface[] */
    private array $builderFactories = [];

    public function __construct(
        private readonly MetadataProviderInterface $metadataProvider
    ) {
    }

    public function addBuilderFactory(DocumentBuilderFactoryInterface $builder): void
    {
        $this->builderFactories[$builder::getEntityClass()] = $builder;
    }

    /**
     * @throws \Exception
     */
    public function create(IndexableEntityInterface $entity): DocumentInterface
    {
        return $this->getBuilder($entity)->build($entity);
    }

    /**
     * @throws NotFoundBuilderFactoryException
     * @throws NotFoundMetadataIndexException
     */
    private function getBuilder(IndexableEntityInterface $entity): DocumentBuilderInterface
    {
        $entityClass = get_class($entity);

        if (isset($this->builders[$entityClass])) {
            return $this->builders[$entityClass];
        }

        if (!isset($this->builderFactories[$entityClass])) {
            if (!isset($this->builderFactories[DefaultDocumentBuilderFactory::DEFAULT])) {
                throw new NotFoundBuilderFactoryException($entityClass);
            }
            $this->builderFactories[$entityClass] = $this->builderFactories[DefaultDocumentBuilderFactory::DEFAULT];
        }

        /** @var \Elasticsearch\Mapping\Index|null $metadataIndex */
        $metadataIndex = $this->metadataProvider->getMappingMetadata()->getIndexByClasss($entityClass);
        if (null === $metadataIndex) {
            throw new NotFoundMetadataIndexException($entityClass);
        }

        $builderFactory = $this->builderFactories[$entityClass];
        $this->builders[$entityClass] = $builderFactory->create($metadataIndex);

        return $this->builders[$entityClass];
    }
}
