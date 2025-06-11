<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\Language;

/**
 * @deprecated Use StemmerFilter instead
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class StemmerAbstractFilter extends AbstractFilter
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
