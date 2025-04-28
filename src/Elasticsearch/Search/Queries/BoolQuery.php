<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use Elasticsearch\Search\Queries\Enums\BoolType;
use Generator;

class BoolQuery implements Query
{
    /** @var Query[] */
    protected array $must = [];

    /** @var Query[] */
    protected array $filter = [];

    /** @var Query[] */
    protected array $should = [];

    /** @var Query[] */
    protected array $must_not = [];

    protected null|int|string $minimum_should_match = null;

    protected null|float $boost = null;

    public function add(Query $query, BoolType $type = BoolType::MUST): self
    {
        $this->{$type->value}[] = $query;

        return $this;
    }

    public function toArray(): Generator
    {
        $bool = [
            BoolType::MUST->value     => array_map(static function (Query $query) {
                return iterator_to_array($query->toArray());
            }, $this->must),
            BoolType::FILTER->value   => array_map(static function (Query $query) {
                return iterator_to_array($query->toArray());
            }, $this->filter),
            BoolType::SHOULD->value   => array_map(static function (Query $query) {
                return iterator_to_array($query->toArray());
            }, $this->should),
            BoolType::MUST_NOT->value => array_map(static function (Query $query) {
                return iterator_to_array($query->toArray());
            }, $this->must_not),
        ];

        if (null !== $this->minimum_should_match) {
            $bool['minimum_should_match'] = $this->minimum_should_match;
        }

        if (null !== $this->boost) {
            $bool['boost'] = $this->boost;
        }

        yield 'bool' => array_filter($bool);
    }
}
