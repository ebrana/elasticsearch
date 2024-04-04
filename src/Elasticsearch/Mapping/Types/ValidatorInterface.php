<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types;

use RuntimeException;

interface ValidatorInterface
{
    /**
     * @throws RuntimeException
     */
    public function validate(): void;
}