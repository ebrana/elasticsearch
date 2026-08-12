<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Jako letter tokenizer, navic tokeny prevede na mala pismena.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lowercase-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class LowercaseTokenizer extends AbstractTokenizer
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'lowercase');
    }
}
