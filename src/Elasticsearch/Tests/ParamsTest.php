<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Connection\Params\CountParams;
use Elasticsearch\Connection\Params\ExpandWildcards;
use Elasticsearch\Connection\Params\IndexDocumentParams;
use Elasticsearch\Connection\Params\OpType;
use Elasticsearch\Connection\Params\Operator;
use Elasticsearch\Connection\Params\SearchParams;
use Elasticsearch\Search\Suggest\Enums\SuggestMode;
use PHPUnit\Framework\TestCase;

class ParamsTest extends TestCase
{
    public function testOnlyFilledParamsAreSerialized(): void
    {
        $this->assertSame([], (new SearchParams())->toArray());
        $this->assertSame(['routing' => 'shard-1'], (new SearchParams(routing: 'shard-1'))->toArray());
    }

    public function testBackedEnumsSerializeToTheirValue(): void
    {
        // assertEquals, protoze poradi klicu urcuje getParams() a na query parametrech nezalezi
        $this->assertEquals(
            [
                'expand_wildcards' => 'hidden,open',
                'default_operator' => 'AND',
                'suggest_mode'     => 'popular',
                'suggest_field'    => 'name',
            ],
            (new SearchParams(
                expand_wildcards: ExpandWildcards::HIDDEN_OPEN,
                default_operator: Operator::AND,
                suggest_field: 'name',
                suggest_mode: SuggestMode::POPULAR,
            ))->toArray()
        );
    }

    public function testSuggestModeIsSharedWithRequestBody(): void
    {
        // stejny enum se pouziva v tele requestu i jako query parametr
        $params = new SearchParams(suggest_mode: SuggestMode::ALWAYS);

        $this->assertSame(['suggest_mode' => 'always'], $params->toArray());
    }

    public function testEnumsInOtherParamObjects(): void
    {
        $this->assertSame(
            ['op_type' => 'create'],
            (new IndexDocumentParams(op_type: OpType::CREATE))->toArray()
        );

        $this->assertEquals(
            ['expand_wildcards' => 'closed', 'min_score' => 3],
            (new CountParams(min_score: 3, expand_wildcards: ExpandWildcards::CLOSED))->toArray()
        );
    }
}
