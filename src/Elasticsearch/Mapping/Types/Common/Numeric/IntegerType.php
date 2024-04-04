<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Attribute;

#[Attribute]
final class IntegerType extends AbstractNumericType
{
    protected const TYPE = 'integer';
}
