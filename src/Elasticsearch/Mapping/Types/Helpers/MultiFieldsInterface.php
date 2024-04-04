<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

interface MultiFieldsInterface
{
    public function getFields(): ArrayCollection;
    public function addField(AbstractType $field): void;
}