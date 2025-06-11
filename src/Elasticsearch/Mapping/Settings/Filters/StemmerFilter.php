<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\Language;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/8.8/analysis-stemmer-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class StemmerFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private readonly Language $language = Language::ENGLISH
    ) {
        parent::__construct($name, 'stemmer');
    }

    public function getLanguage(): string
    {
        return $this->language->value;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['language'] = $this->getLanguage();

        return $data;
    }
}
