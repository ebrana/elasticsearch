<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Sorts;

use Generator;

class ScriptSort implements SortInterface
{
    private const string FIELD = '_script';

    /**
     * @param array<string, mixed>|null $params
     */
    public function __construct(
        private readonly string $source,
        private readonly string $lang = 'painless',
        private readonly ?array $params = null,
        private readonly ?SortDirection $order = null,
    ) {
    }

    public function toArray(): Generator
    {
        $payload = [];

        if ($this->order) {
            $payload['order'] = $this->order->value;
        }

        $payload['script'] = [
            'lang' => $this->lang,
            'source' => $this->source,
        ];

        if (null !== $this->params && count($this->params) > 0) {
            $payload['script']['params'] = $this->params;
        }

        yield self::FIELD => $payload;
    }
}
