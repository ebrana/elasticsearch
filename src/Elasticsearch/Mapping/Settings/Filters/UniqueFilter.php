<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-unique-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class UniqueFilter extends AbstractFilter
{
    public function __construct(
        string $name,
        private bool $only_on_same_position = false,
    ) {
        parent::__construct($name, 'unique');
    }

    public function isOnlyOnSamePosition(): bool
    {
        return $this->only_on_same_position;
    }

    public function setOnlyOnSamePosition(bool $only_on_same_position): void
    {
        $this->only_on_same_position = $only_on_same_position;
    }

    /**
     * @return array<string, array<string>|bool|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->only_on_same_position) {
            $data['only_on_same_position'] = true;
        }

        return $data;
    }
}
