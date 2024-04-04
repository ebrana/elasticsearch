<?php

declare(strict_types=1);

namespace Elasticsearch\Debug;

use Closure;
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
        ];
    }

    /**
     * @return array<int, array<string|float|null>>
     */
    public function getData(): array
    {
        foreach ($this->data as $idx => $data) {
            if (is_callable($data['executionMS'])) {
                $this->data[$idx]['executionMS'] = $data['executionMS']();
            }
        }

        return $this->data;
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
