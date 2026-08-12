<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Indexing\Bulk\BulkRequest;
use Elasticsearch\Indexing\Bulk\BulkResponse;
use Elasticsearch\Indexing\Bulk\CreateOperation;
use Elasticsearch\Indexing\Bulk\DeleteOperation;
use Elasticsearch\Indexing\Bulk\IndexOperation;
use Elasticsearch\Indexing\Bulk\UpdateOperation;
use Elasticsearch\Indexing\Document;
use Elasticsearch\Mapping\Index;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BulkTest extends TestCase
{
    private function createDocument(string $id, string $name): Document
    {
        $document = new Document(new Index('product'), $id);
        $document->set('name', $name);

        return $document;
    }

    public function testRequestCollectsOperations(): void
    {
        $index = new Index('product');
        $request = new BulkRequest();

        $this->assertTrue($request->isEmpty());

        $request->index($this->createDocument('1', 'boty'))
            ->create($this->createDocument('2', 'bunda'))
            ->update($index, '3', ['price' => 100], docAsUpsert: true)
            ->delete($index, '4');

        $this->assertFalse($request->isEmpty());
        $this->assertCount(4, $request);
        $this->assertSame(
            ['index', 'create', 'update', 'delete'],
            array_map(static fn ($operation): string => $operation->getAction(), $request->getOperations())
        );
    }

    public function testIndexOperationShape(): void
    {
        $operation = new IndexOperation($this->createDocument('1', 'boty'), ['routing' => 'shard-1']);

        $this->assertSame('index', $operation->getAction());
        $this->assertSame('1', $operation->getId());
        $this->assertSame('product', $operation->getIndex()->getName());
        $this->assertSame(['routing' => 'shard-1'], $operation->getMetadata());
        $this->assertSame(['name' => 'boty'], $operation->getSource());
    }

    public function testCreateOperationShape(): void
    {
        $operation = new CreateOperation($this->createDocument('1', 'boty'));

        $this->assertSame('create', $operation->getAction());
        $this->assertSame(['name' => 'boty'], $operation->getSource());
    }

    public function testDeleteOperationHasNoSource(): void
    {
        $operation = new DeleteOperation(new Index('product'), '1');

        $this->assertSame('delete', $operation->getAction());
        $this->assertNull($operation->getSource());
    }

    public function testUpdateOperationWithDoc(): void
    {
        $operation = new UpdateOperation(
            new Index('product'),
            '1',
            doc: ['price' => 100],
            docAsUpsert: true,
            retryOnConflict: 3
        );

        $this->assertSame('update', $operation->getAction());
        $this->assertSame(['retry_on_conflict' => 3], $operation->getMetadata());
        $this->assertSame(['doc' => ['price' => 100], 'doc_as_upsert' => true], $operation->getSource());
    }

    public function testUpdateOperationWithScriptAndUpsert(): void
    {
        $operation = new UpdateOperation(
            new Index('product'),
            '1',
            script: ['source' => 'ctx._source.views++'],
            upsert: ['views' => 1]
        );

        $this->assertSame([
            'script' => ['source' => 'ctx._source.views++'],
            'upsert' => ['views' => 1],
        ], $operation->getSource());
    }

    public function testUpdateOperationRequiresDocOrScript(): void
    {
        $this->expectException(RuntimeException::class);

        (new UpdateOperation(new Index('product'), '1'))->getSource();
    }

    public function testUpdateOperationRejectsBothDocAndScript(): void
    {
        $this->expectException(RuntimeException::class);

        (new UpdateOperation(
            new Index('product'),
            '1',
            doc: ['a' => 1],
            script: ['source' => 'x']
        ))->getSource();
    }

    public function testResponseWithoutErrors(): void
    {
        $response = new BulkResponse([
            'took'   => 12,
            'errors' => false,
            'items'  => [
                ['index' => ['_index' => 'product', '_id' => '1', 'status' => 201]],
                ['index' => ['_index' => 'product', '_id' => '2', 'status' => 200]],
            ],
        ]);

        $this->assertSame(12, $response->getTook());
        $this->assertFalse($response->hasErrors());
        $this->assertCount(2, $response);
        $this->assertSame(2, $response->getSuccessCount());
        $this->assertSame([], $response->getErrors());
    }

    public function testResponseCollectsItemErrors(): void
    {
        // bulk vraci HTTP 200 i pri castecnem selhani
        $response = new BulkResponse([
            'took'   => 5,
            'errors' => true,
            'items'  => [
                ['index' => ['_index' => 'product', '_id' => '1', 'status' => 201]],
                ['create' => [
                    '_index' => 'product',
                    '_id'    => '2',
                    'status' => 409,
                    'error'  => ['type' => 'version_conflict_engine_exception', 'reason' => 'already exists'],
                ]],
            ],
        ]);

        $this->assertTrue($response->hasErrors());
        $this->assertCount(2, $response);
        $this->assertSame(1, $response->getSuccessCount());

        $errors = $response->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('create', $errors[0]->getAction());
        $this->assertSame('2', $errors[0]->getId());
        $this->assertSame(409, $errors[0]->getStatus());
        $this->assertSame('version_conflict_engine_exception', $errors[0]->getType());
        $this->assertStringContainsString('already exists', (string)$errors[0]);
    }
}
