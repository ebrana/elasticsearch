<?php declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class HalfFloatType extends AbstractNumericType
{
    protected const TYPE = 'half_float';
}
