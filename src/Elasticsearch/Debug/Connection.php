<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

use Elastic\Elasticsearch\ClientBuilder;
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

    public function hasIndex(Index $index): bool
    {
        $query = new Query('HEAD /' . $index->getNameWithPrefix($this->getIndexPrefix()));
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            $hasIndex = parent::hasIndex($index);
            $query->setBoolResult($hasIndex);
        } finally {
            $query->stop();
        }

        return $hasIndex;
    }

    public function createIndex(MetadataRequest $request): void
    {
        $index = $request->getIndex();
        $query = new Query('PUT /' . $index->getNameWithPrefix($this->getIndexPrefix()));
        $query->setBody($request->getMappingJson());
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::createIndex($request);
        } finally {
            $query->stop();
        }
    }

    public function deleteIndex(Index $index): void
    {
        $query = new Query('DELETE /' . $index->getNameWithPrefix($this->getIndexPrefix()));
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::deleteIndex($index);
        } finally {
            $query->stop();
        }
    }

    public function indexDocument(DocumentInterface $document): void
    {
        $type = $document->getId() !== null ? 'PUT' : 'POST';
        $index = $document->getIndex();
        $query = sprintf('%s /%s/_doc/%s',
            $type,
            $index->getNameWithPrefix($this->getIndexPrefix()),
            $document->getId());
        $query = new Query($query);
        $query->setBody($document->toJson());
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            parent::indexDocument($document);
        } finally {
            $query->stop();
        }
    }

    public function count(Builder $builder): int
    {
        $params = $builder->build(false, false)->toArray();
        $method = empty($params['body']) ? 'GET ' : 'POST ';
        $url = '/_count';
        if (isset($params['index'])) {
            $url = '/' . $params['index'] . '/_count';
        }

        $query = new Query($method . $url);
        $this->debugDataHolder->addQuery($query);
        $query->start();

        try {
            $count = parent::count($builder);
            $query->setCountResult($count);
        } finally {
            $query->stop();
        }

        return $count;
    }

    public function search(Builder $builder): Result
    {
        $params = $builder->build()->toArray();
        $method = empty($params['body']) ? 'GET ' : 'POST ';
        if (isset($params['index'])) {
            $url = '/' . $params['index'] . '/_search';
        } else {
            $url = '/_search';
        }
        $bodyArr = [
            'body' => $params['body'],
        ];
        if (isset($params['size'])) {
            $bodyArr['size'] = $params['size'];
        }
        if (isset($params['from'])) {
            $bodyArr['from'] = $params['from'];
        }
        $query = new Query($method . $url);
        $body = json_encode($bodyArr, JSON_THROW_ON_ERROR);
        try {
            $query->setBody($body);
            $this->debugDataHolder->addQuery($query);
            $query->start();
            $result = parent::search($builder);
            $query->setResult($result);
        } finally {
            $query->stop();
        }

        return $result;
    }
}
