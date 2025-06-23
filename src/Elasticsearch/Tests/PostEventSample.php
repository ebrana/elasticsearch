<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Mapping\Drivers\Events\PostEventInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Types\Text\TextType;

class PostEventSample implements PostEventInterface
{
    public function postCreateIndex(Index $index): void
    {
        $field = new TextType(name: 'postEventName');
        $field->setFieldName('postEventName');
        $index->addProperty($field);
    }
}
