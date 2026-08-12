<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

use RuntimeException;

/**
 * Sekce `suggest` v tele requestu. Muze obsahovat vic pojmenovanych suggesteru naraz;
 * v odpovedi se pak kazdy vraci pod svym jmenem.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html
 */
final class Suggest
{
    /** @var SuggestInterface[] */
    private array $suggesters = [];

    public function __construct(SuggestInterface ...$suggesters)
    {
        foreach ($suggesters as $suggester) {
            $this->add($suggester);
        }
    }

    public function add(SuggestInterface $suggester): self
    {
        $this->suggesters[$suggester->getName()] = $suggester;

        return $this;
    }

    /**
     * @return SuggestInterface[]
     */
    public function getSuggesters(): array
    {
        return $this->suggesters;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ([] === $this->suggesters) {
            throw new RuntimeException('Suggest must define at least one suggester.');
        }

        $data = [];
        foreach ($this->suggesters as $name => $suggester) {
            $data[$name] = $suggester->toArray();
        }

        return $data;
    }
}
