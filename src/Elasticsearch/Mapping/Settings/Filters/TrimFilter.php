<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-trim-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class TrimFilter extends AbstractFilter
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'trim');
    }
}
