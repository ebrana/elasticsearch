<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

use Elastic\Elasticsearch\ClientBuilder;
use Elasticsearch\Connection\Params\AbstractParams;
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

class Connection extends \Elasticsearch\Connection\Connection
{
    public function __construct(
        private readonly DebugDataHolder $debugDataHolder,
        ClientBuilder $clientBuilder,
        string $indexPrefix = '',
    ) {
        parent::__construct($clientBuilder, $indexPrefix);
    }

    public function hasIndex(Index $index, ?IndexExistParams $params = null): bool
    {
        $url = 'HEAD /' . $index->getNameWithPrefix($this->getIndexPrefix());
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($url);
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            $hasIndex = parent::hasIndex($index, $params);
            $query->setBoolResult($hasIndex);
        } finally {
            $query->stop();
        }

        return $hasIndex;
    }

    public function createIndex(MetadataRequest $request, ?CreateIndexParams $params = null): void
    {
        $index = $request->getIndex();
        $url = 'PUT /' . $index->getNameWithPrefix($this->getIndexPrefix());
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($url);
        $query->setBody($request->getMappingJson());
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::createIndex($request, $params);
        } finally {
            $query->stop();
        }
    }

    public function deleteIndex(Index $index, ?DeleteIndexParams $params = null): void
    {
        $url = 'DELETE /' . $index->getNameWithPrefix($this->getIndexPrefix());
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($url);
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::deleteIndex($index, $params);
        } finally {
            $query->stop();
        }
    }

    public function indexDocument(DocumentInterface $document, ?IndexDocumentParams $params = null): void
    {
        $type = $document->getId() !== null ? 'PUT' : 'POST';
        $index = $document->getIndex();
        $url = sprintf('%s /%s/_doc/%s',
            $type,
            $index->getNameWithPrefix($this->getIndexPrefix()),
            $document->getId());
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($url);
        $query->setBody($document->toJson());
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::indexDocument($document, $params);
        } finally {
            $query->stop();
        }
    }

    public function count(Builder $builder, ?CountParams $params = null): int
    {
        $data = $builder->build(false, false)->toArray();
        $method = empty($data['body']) ? 'GET ' : 'POST ';
        $url = '/_count';
        if (isset($data['index'])) {
            $url = '/' . $data['index'] . '/_count';
        }
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($method . $url);
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            $count = parent::count($builder, $params);
            $query->setCountResult($count);
        } finally {
            $query->stop();
        }

        return $count;
    }

    public function search(Builder $builder, ?SearchParams $params = null): Result
    {
        $data = $builder->build()->toArray();
        $method = empty($data['body']) ? 'GET ' : 'POST ';
        if (isset($data['index'])) {
            $url = '/' . $data['index'] . '/_search';
        } else {
            $url = '/_search';
        }
        $bodyArr = [
            'body' => $data['body'],
        ];
        if (isset($data['size'])) {
            $bodyArr['size'] = $data['size'];
        }
        if (isset($data['from'])) {
            $bodyArr['from'] = $data['from'];
        }
        $this->addParamsIntoQuery($url, $params);
        $query = new Query($method . $url);
        $body = json_encode($bodyArr, JSON_THROW_ON_ERROR);
        try {
            $query->setBody($body);
            $this->debugDataHolder->addQuery($query);
            $query->start();
            $result = parent::search($builder, $params);
            $query->setResult($result);
        } finally {
            $query->stop();
        }

        return $result;
    }

    private function addParamsIntoQuery(string &$query, ?AbstractParams $params = null): void
    {
        if ($params) {
            $query .= sprintf('?%s', http_build_query($params->toArray()));
        }
    }
}
