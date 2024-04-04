<?php declare(strict_types=1);

namespace Elasticsearch\Tests\Entity\Abstracted;

use Elasticsearch\Mapping\Types\Common\BooleanType;

abstract class WrapperAbstractAddress extends AbstractAddress
{
    /**
     * @var int
     * @BooleanType(
     *     store=true
     * )
     */
    protected $zip;
}
