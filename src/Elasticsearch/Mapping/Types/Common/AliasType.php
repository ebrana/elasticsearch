<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class AliasType extends AbstractType
{
    public function __construct(
        private readonly string $path,
        ?string $name = null,
        ?string $contect = null,
    ) {
        parent::__construct();

        $this->context = $contect;
        $this->type = 'alias';
        if ($name && $name !== '') {
            $this->setName($name);
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $collection->set('path', $this->getPath());

        return $collection;
    }
}
