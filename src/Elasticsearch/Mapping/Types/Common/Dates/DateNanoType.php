<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Dates;

use Attribute;
use Elasticsearch\Mapping\Types\AbstractType;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class DateNanoType extends AbstractType
{
    public function __construct(
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'date_nanos';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }
}
