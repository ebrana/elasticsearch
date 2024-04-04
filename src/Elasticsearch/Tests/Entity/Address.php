<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Mapping\Types\Helpers\OnScriptError;
use Elasticsearch\Tests\Entity\Abstracted\WrapperAbstractAddress;
use Elasticsearch\Mapping\Types\Common\BooleanType;
use Elasticsearch\Mapping\Types\Helpers\Metadata;

class Address extends WrapperAbstractAddress
{
    #[BooleanType(store: true, on_script_error: OnScriptError::FAIL, meta: new Metadata(unit: 'test', metric_type: 'metric'))]
    protected bool $isMain = false;

    public function isMain(): bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): void
    {
        $this->isMain = $isMain;
    }
}
