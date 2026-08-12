<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Queries;

use RuntimeException;

/**
 * Jako terms query, ale vyzaduje jen zadany minimalni pocet shod. Kolik jich musi byt,
 * se bere z pole dokumentu (minimum_should_match_field), ze skriptu
 * (minimum_should_match_script), nebo se zada pevne (minimum_should_match).
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-set-query.html
 */
readonly class TermsSetQuery implements Query
{
    /**
     * @param string[] $terms
     * @param array<string, mixed>|null $minimum_should_match_script
     */
    public function __construct(
        private string $field,
        private array $terms,
        private ?string $minimum_should_match_field = null,
        private ?array $minimum_should_match_script = null,
        private int|string|null $minimum_should_match = null,
        private ?float $boost = null,
    ) {
    }

    public function toArray(): array
    {
        if (
            null === $this->minimum_should_match_field
            && null === $this->minimum_should_match_script
            && null === $this->minimum_should_match
        ) {
            throw new RuntimeException(
                'Terms set query must define minimum_should_match_field, minimum_should_match_script'
                . ' or minimum_should_match.'
            );
        }

        $data = ['terms' => $this->terms];

        if (null !== $this->minimum_should_match_field) {
            $data['minimum_should_match_field'] = $this->minimum_should_match_field;
        }

        if (null !== $this->minimum_should_match_script) {
            $data['minimum_should_match_script'] = $this->minimum_should_match_script;
        }

        if (null !== $this->minimum_should_match) {
            $data['minimum_should_match'] = $this->minimum_should_match;
        }

        if (null !== $this->boost) {
            $data['boost'] = $this->boost;
        }

        return ['terms_set' => [$this->field => $data]];
    }
}
