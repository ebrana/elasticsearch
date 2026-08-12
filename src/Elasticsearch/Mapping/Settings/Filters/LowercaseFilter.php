<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;
use Elasticsearch\Mapping\Settings\Filters\Enums\LowercaseLanguage;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lowercase-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class LowercaseFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private ?LowercaseLanguage $language = null,
    ) {
        parent::__construct($name, 'lowercase');
    }

    public function getLanguage(): ?LowercaseLanguage
    {
        return $this->language;
    }

    public function setLanguage(?LowercaseLanguage $language): void
    {
        $this->language = $language;
    }

    /**
     * @return array<string, array<string>|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->language) {
            $data['language'] = $this->language->value;
        }

        return $data;
    }
}
