<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Tokenizers;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractTokenizer;

/**
 * Rozpada text vsude, kde znak neni pismeno.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-letter-tokenizer.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class LetterTokenizer extends AbstractTokenizer
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'letter');
    }
}
