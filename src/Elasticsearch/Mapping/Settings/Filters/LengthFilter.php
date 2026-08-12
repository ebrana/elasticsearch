<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Filters;

use Attribute;
use Elasticsearch\Mapping\Settings\AbstractFilter;

/**
 * Vyhodi tokeny mimo zadany rozsah delky.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-length-tokenfilter.html
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class LengthFilter extends AbstractFilter
{
    public const int DEFAULT_MIN = 0;
    public const int DEFAULT_MAX = 2147483647;

    public function __construct(
        string $name,
        private int $min = self::DEFAULT_MIN,
        private int $max = self::DEFAULT_MAX,
    ) {
        parent::__construct($name, 'length');
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function setMax(int $max): void
    {
        $this->max = $max;
    }

    /**
     * @return array<string, array<string>|int|string>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (self::DEFAULT_MIN !== $this->min) {
            $data['min'] = $this->min;
        }

        if (self::DEFAULT_MAX !== $this->max) {
            $data['max'] = $this->max;
        }

        return $data;
    }
}
