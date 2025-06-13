<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Builders;

use Elasticsearch\Indexing\Document;
use Elasticsearch\Indexing\Exceptions\DataResolverExistsException;
use Elasticsearch\Indexing\Interfaces\DocumentBuilderInterface;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Indexing\Resolvers\CollectionByMappingResolverTrait;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\ObjectType;
use InvalidArgumentException;
use RuntimeException;

readonly class DefaultDocumentBuilder implements DocumentBuilderInterface
{
    use CollectionByMappingResolverTrait;

    public function __construct(private Index $index)
    {
    }

    /**
     * @throws \Elasticsearch\Indexing\Exceptions\DocumentToJsonException
     * @throws \Elasticsearch\Indexing\Exceptions\DataResolverExistsException
     */
    public function build(IndexableEntityInterface $entity): DocumentInterface
    {
        $properties = $this->index->getProperties();
        $id = null;
        if ($properties->containsKey('id')) {
            if (!method_exists($entity, 'getId')) {
                throw new DataResolverExistsException(sprintf('Entity "%s" does not have method "%s(...)".', get_class($entity), 'getId'));
            }
            /** @var scalar $id */
            $id = $entity->getId();
            $id = (string)$id;
        }
        $document = new Document($this->index, $id);

        foreach ($this->index->getProperties() as $property) {
            $propertyName = $property->getFieldName();
            if (null === $propertyName) {
                throw new RuntimeException('Property field name empty.');
            }
            $getter = $this->getGetter($entity, $property);
            if (!method_exists($entity, $getter)) {
                throw new DataResolverExistsException(sprintf('Entity "%s" does not have method "%s(...)".', get_class($entity), $getter));
            }

            $data = $entity->$getter();
            if ($property instanceof ObjectType && $property->getMappedBy()) {
                if (!is_iterable($data)) {
                    throw new InvalidArgumentException('Resolving data for instance of ObjectType must be iterable.');
                }
                /** @var IndexableEntityInterface[] $typedData */
                $typedData = $data;
                $data = $this->resolveCollection($property, $typedData);
            } else if (!is_scalar($data) && null !== $data) {
                throw new RuntimeException(
                    sprintf('Resolved value must be null or scalar. Entity "%s" and property "%s". Write your custom resolver instead.',
                        get_class($entity),
                        $property->getFieldName()
                    )
                );
            }
            $document->set($propertyName, $data);
        }

        return $document;
    }
}
