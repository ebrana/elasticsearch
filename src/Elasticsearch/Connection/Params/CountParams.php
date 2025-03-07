<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

final class CountParams extends AbstractParams
{

    public function __construct(
        protected ?bool $ignore_unavailable = null,
        protected ?bool $ignore_throttled = null,
        protected ?bool $allow_no_indices = null,
        protected ?int $min_score = null,
        protected ?string $preference = null,
        protected ?string $routing = null,
        protected ?string $q = null,
        protected ?string $analyzer = null,
        protected ?bool $analyze_wildcard = null,
        protected ?string $df = null,
        protected ?bool $lenient = null,
        protected ?int $terminate_after = null,
        protected ?bool $pretty = null,
        protected ?bool $human = null,
        protected ?bool $error_trace = null,
        protected ?string $source = null,
        protected ?string $filter_path = null,
        protected ?ExpandWildcards $expand_wildcards = null,
        protected ?Operator $default_operator = null,
    ) {
    }

    protected function getParams(): Generator
    {
        yield from [
            'ignore_unavailable',
            'ignore_throttled',
            'allow_no_indices',
            'expand_wildcards',
            'min_score',
            'preference',
            'routing',
            'q',
            'analyzer',
            'analyze_wildcard',
            'default_operator',
            'df',
            'lenient',
            'terminate_after',
            'pretty',
            'human',
            'error_trace',
            'source',
            'filter_path'
        ];
    }
}
