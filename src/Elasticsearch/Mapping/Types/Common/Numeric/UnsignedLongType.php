<?php declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Numeric;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class UnsignedLongType extends AbstractNumericType
{
    protected const TYPE = 'unsigned_long';
}
