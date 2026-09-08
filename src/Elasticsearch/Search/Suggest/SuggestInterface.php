<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

/**
 * A single named suggester in the `suggest` section. It is then returned under its own name
 * in the response as well.
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
