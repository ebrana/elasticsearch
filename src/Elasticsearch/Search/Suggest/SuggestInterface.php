<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

/**
 * Jeden pojmenovany suggester v sekci `suggest`. Pod svym jmenem se pak vraci i v odpovedi.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html
 */
interface SuggestInterface
{
    public function getName(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
