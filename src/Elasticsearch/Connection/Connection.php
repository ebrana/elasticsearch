<?php

declare(strict_types=1);

namespace Elasticsearch\Connection;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Endpoints\Indices;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Elasticsearch\Indexing\Interfaces\DocumentInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Request\MetadataRequest;
use Elasticsearch\Search\Builder;
use Elasticsearch\Search\Results\Result;
use RuntimeException;

class Connection
{
    private ?Client $client = null;

    public function __construct(
        private readonly ClientBuilder $clientBuilder,
        private readonly string $indexPrefix = ''
    ) {
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\MissingParameterException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     * @throws \Elasticsearch\Mapping\Exceptions\EmptyIndexNameException
     */
    public function hasIndex(Index $index): bool
    {
        $response = $this->indices()->exists([
            'index' => $index->getNameWithPrefix($this->indexPrefix),
        ]);
        if ($response instanceof Elasticsearch) {
            return $response->asBool();
        }
        throw new RuntimeException('Wrong data format');
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\MissingParameterException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     * @throws \Elasticsearch\Mapping\Exceptions\EmptyIndexNameException
     */
    public function createIndex(MetadataRequest $request): void
    {
        $params = [
            'index' => $request->getIndex()->getNameWithPrefix($this->indexPrefix),
            'body'  => $request->getMappingJson(),
        ];

        $this->indices()->create($params);
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\MissingParameterException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     * @throws \Elasticsearch\Mapping\Exceptions\EmptyIndexNameException
     */
    public function deleteIndex(Index $index): void
    {
        $this->indices()->delete([
            'index' => $index->getNameWithPrefix($this->indexPrefix),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function indexDocument(DocumentInterface $document): void
    {
        $record = [
            'index' => $document->getIndex()->getNameWithPrefix($this->indexPrefix),
            'type'  => '_doc',
            'body'  => $document->toJson(),
        ];

        $documentId = $document->getId();
        if (null !== $documentId) {
            $record['id'] = $documentId;
        }

        $this->getClient()->index($record);
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function count(Builder $builder): int
    {
        $response = $this->getClient()->count($builder->build(false, false)->toArray());
        if ($response instanceof Elasticsearch) {
            $data = $response->asArray();

            return $data['count'] ?? 0;
        }

        throw new RuntimeException('Wrong data format');
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function search(Builder $builder): Result
    {
        $response = $this->getClient()->search($builder->build()->toArray());
        if ($response instanceof Elasticsearch) {
            return new Result($response->asArray());
        }

        throw new RuntimeException('Wrong data format');
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     */
    public function getClient(): Client
    {
        if (null === $this->client) {
            $this->client = $this->clientBuilder->build();
        }

        return $this->client;
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     */
    public function indices(): Indices
    {
        return $this->getClient()->indices();
    }

    public function getIndexPrefix(): string
    {
        return $this->indexPrefix;
    }

    /**
     * @return array{name: string, cluster_name: string, cluster_id: string, version: string[], tagline: string}
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function getServerInfo(): array
    {
        $response = $this->getClient()->info();
        if ($response instanceof Elasticsearch) {
            /** @var array{name: string, cluster_name: string, cluster_id: string, version: string[], tagline: string} $result */
            $result = $response->asArray();

            return $result;
        }

        throw new RuntimeException('Information getting error.');
    }
}
