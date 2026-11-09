<?php

declare(strict_types=1);

namespace Elasticsearch\Tests\Entity;

use Elasticsearch\Indexing\Interfaces\IndexableEntityInterface;
use Elasticsearch\Mapping\Index;
use Elasticsearch\Mapping\Settings\MappingSettings;
use Elasticsearch\Mapping\Types\Common\Keywords\KeywordType;
use Elasticsearch\Mapping\Types\ObjectsAndRelational\Dynamic;

#[Index(name: "Catalog", dynamic: Dynamic::FALSE)]
#[MappingSettings(total_fields_limit: 2000, depth_limit: 5, total_fields_ignore_dynamic_beyond_limit: true)]
class Catalog implements IndexableEntityInterface
{
    #[KeywordType]
    private string $id = '1';

    public function getId(): string
    {
        return $this->id;
    }
}
