<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Common\Dates;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;
use Elasticsearch\Mapping\Types\Helpers\DateOptionsTrait;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class DateNanoType extends AbstractType
{
    use DateOptionsTrait;

    public function __construct(
        ?string $name = null,
        ?string $context = null,
        ?string $format = null,
        ?string $locale = null,
        ?string $null_value = null,
        bool $index = true,
        bool $doc_values = true,
        bool $store = false,
        bool $ignore_malformed = false,
    ) {
        parent::__construct();

        $this->format = $format;
        $this->locale = $locale;
        $this->null_value = $null_value;
        $this->index = $index;
        $this->doc_values = $doc_values;
        $this->store = $store;
        $this->ignore_malformed = $ignore_malformed;

        $this->context = $context;
        $this->type = 'date_nanos';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();
        $this->provideDateOptions($collection);

        return $collection;
    }
}
