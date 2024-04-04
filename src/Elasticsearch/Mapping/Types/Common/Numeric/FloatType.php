<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_PROPERTY)]
final class FloatType extends AbstractNumericType
{
    protected const TYPE = 'float';
}
