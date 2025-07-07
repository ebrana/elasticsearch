<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

use Closure;
use Elasticsearch\Search\Results\Result;
use function is_callable;

class DebugDataHolder
{
    /** @var array<int, array<string|float|null|Closure>> */
    private array $data = [];

    public function addQuery(Query $query): void
    {
        $this->data[] = [
            'query'       => $query->getQuery(),
            'body'        => $query->getBody(),
            'executionMS' => $query->getDuration(...),
            'boolResult'  => $query->getBoolResult(...),
            'countResult' => $query->getCountResult(...),
            'result'      => $query->getResult(...),
        ];
    }

    /**
     * @return array<int, array<float|string|null|bool|int|Result>>
     */
    public function getData(): array
    {
        $returnData = [];

        foreach ($this->data as $idx => $data) {
            $returnData[$idx] = [];
            if (is_callable($data['executionMS'])) {
                /** @var float $typedData */
                $typedData = call_user_func($data['executionMS']);
                $returnData[$idx]['executionMS'] = $typedData;
            }
            if (is_callable($data['boolResult'])) {
                /** @var bool $typedData */
                $typedData = call_user_func($data['boolResult']);
                $returnData[$idx]['boolResult'] = $typedData;
            }
            if (is_callable($data['countResult'])) {
                /** @var int $typedData */
                $typedData = call_user_func($data['countResult']);
                $returnData[$idx]['countResult'] = $typedData;
            }
            if (is_callable($data['result'])) {
                /** @var Result $typedData */
                $typedData = call_user_func($data['result']);
                $returnData[$idx]['result'] = $typedData;
            }
            /** @var string $query */
            $query = $data['query'];
            $returnData[$idx]['query'] = $query;

            /** @var string|null $body */
            $body = $data['body'];

            $returnData[$idx]['body'] = $body;
        }

        return $returnData;
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
