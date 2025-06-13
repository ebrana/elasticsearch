<?php

declare(strict_types=1);

namespace Elasticsearch\Connection;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Endpoints\Indices;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Elasticsearch\Connection\Params\CountParams;
use Elasticsearch\Connection\Params\CreateIndexParams;
use Elasticsearch\Connection\Params\DeleteIndexParams;
use Elasticsearch\Connection\Params\IndexDocumentParams;
use Elasticsearch\Connection\Params\IndexExistParams;
use Elasticsearch\Connection\Params\SearchParams;
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
    public function hasIndex(Index $index, ?IndexExistParams $params = null): bool
    {
        $request = [
            'index' => $index->getNameWithPrefix($this->indexPrefix),
        ];
        if ($params) {
            $request = array_merge($request, $params->toArray());
        }

        /** @var array{index: string|array<string>, local?: bool, ignore_unavailable?: bool, allow_no_indices?: bool, expand_wildcards?: string, flat_settings?: bool, include_defaults?: bool, pretty?: bool} $typedRequest */
        $typedRequest = $request;

        $response = $this->indices()->exists($typedRequest);
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
    public function createIndex(MetadataRequest $request, ?CreateIndexParams $params = null): void
    {
        $data = [
            'index' => $request->getIndex()->getNameWithPrefix($this->indexPrefix),
            'body'  => $request->getMappingJson(),
        ];

        if ($params) {
            $data = array_merge($data, $params->toArray());
        }

        /** @var array{index: string, body: string, wait_for_active_shards?: string, timeout?: string, master_timeout?: string} $typedData */
        $typedData = $data;

        $this->indices()->create($typedData);
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\MissingParameterException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     * @throws \Elasticsearch\Mapping\Exceptions\EmptyIndexNameException
     */
    public function deleteIndex(Index $index, ?DeleteIndexParams $params = null): void
    {
        $data = [
            'index' => $index->getNameWithPrefix($this->indexPrefix),
        ];

        if ($params) {
            $data = array_merge($data, $params->toArray());
        }

        /** @var array{index: string|array<string>, timeout?: string, master_timeout?: string, ignore_unavailable?: bool, allow_no_indices?: bool, expand_wildcards?: string} $typedData */
        $typedData = $data;

        $this->indices()->delete($typedData);
    }

    /**
     * @throws \Exception
     */
    public function indexDocument(DocumentInterface $document, ?IndexDocumentParams $params = null): void
    {
        $request = [
            'index' => $document->getIndex()->getNameWithPrefix($this->indexPrefix),
            'type'  => '_doc',
            'body'  => $document->toJson(),
        ];

        $documentId = $document->getId();
        if (null !== $documentId) {
            $request['id'] = $documentId;
        }

        if ($params) {
            $request = array_merge($request, $params->toArray());
        }

        /** @var array{index: string, body: string, type: string, wait_for_active_shards?: string, timeout?: string, master_timeout?: string} $typedData */
        $typedData = $request;

        $this->getClient()->index($typedData);
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function count(Builder $builder, ?CountParams $params = null): int
    {
        $request = $builder->build(false, false)->toArray();
        if ($params) {
            $request = array_merge($request, $params->toArray());
        }

        /** @var array{index?: string|array<string>, query: string|null, ignore_unavailable?: bool, allow_no_indices?: bool, expand_wildcards?: string, min_score?: int, preference?: string, routing?: string, terminate_after?: int, timeout?: string} $typedData */
        $typedData = $request;

        $response = $this->getClient()->count($typedData);
        if ($response instanceof Elasticsearch) {
            $data = $response->asArray();
            /** @var int $count */
            $count = $data['count'] ?? 0;

            return $count;
        }

        throw new RuntimeException('Wrong data format');
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function search(Builder $builder, ?SearchParams $params = null): Result
    {
        $request = $builder->build()->toArray();
        if ($params) {
            $request = array_merge($request, $params->toArray());
        }

        /** @var array{
         *     index?: string,
         *     from?: int,
         *     size?: int,
         *     query?: string,
         *     aggs?: string,
         *     sort?: string,
         *     _source?: string,
         *     timeout?: string,
         *     terminate_after?: int,
         *     explain?: bool,
         *     track_total_hits?: bool|int,
         *     highlight?: string,
         *     collapse?: string,
         *     from_seq_no?: int,
         *     max_concurrent_shard_requests?: int,
         *     stored_fields?: string,
         *     docvalue_fields?: string,
         *     min_score?: float,
         *     preference?: string,
         *     routing?: string,
         *     scroll?: string,
         *     version?: bool,
         *     seq_no_primary_term?: bool,
         *     stats?: string,
         *     allow_no_indices?: bool,
         *     ignore_unavailable?: bool,
         *     expand_wildcards?: string,
         *     track_scores?: bool,
         * } $typedData
         */
        $typedData = $request;

        $response = $this->getClient()->search($typedData);
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
