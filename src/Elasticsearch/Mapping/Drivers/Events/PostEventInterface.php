<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Events;

use Elasticsearch\Mapping\Index;

interface PostEventInterface
{
    public function postCreateIndex(Index $index): void;
}
