<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractAnalyzer;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-whitespace-analyzer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class WhitespaceAnalyzer extends AbstractAnalyzer
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'whitespace');
    }
}
