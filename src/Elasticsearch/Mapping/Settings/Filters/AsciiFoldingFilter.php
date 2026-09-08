<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Odstrani diakritiku ("Nové" -> "Nove").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-asciifolding-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class AsciiFoldingFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private bool $preserve_original = false,
    ) {
        parent::__construct($name, 'asciifolding');
    }

    public function isPreserveOriginal(): bool
    {
        return $this->preserve_original;
    }

    public function setPreserveOriginal(bool $preserve_original): void
    {
        $this->preserve_original = $preserve_original;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->preserve_original) {
            $data['preserve_original'] = true;
        }

        return $data;
    }
}
