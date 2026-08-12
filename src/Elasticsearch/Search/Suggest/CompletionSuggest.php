<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

use RuntimeException;

/**
 * Autocomplete nad polem typu `completion`. Na rozdil od ostatnich suggesteru se zadava
 * `prefix` (nebo `regex`), ne `text`.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#completion-suggester
 */
readonly class CompletionSuggest implements SuggestInterface
{
    /**
     * @param array<string, mixed>|null $fuzzy napr. ['fuzziness' => 'AUTO', 'transpositions' => true]
     * @param array<string, mixed>|null $contexts
     */
    public function __construct(
        private string $name,
        private string $field,
        private ?string $prefix = null,
        private ?string $regex = null,
        private ?int $size = null,
        private ?bool $skip_duplicates = null,
        private ?array $fuzzy = null,
        private ?array $contexts = null,
        private ?string $analyzer = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        if (null === $this->prefix && null === $this->regex) {
            throw new RuntimeException('Completion suggest must define prefix or regex.');
        }

        if (null !== $this->prefix && null !== $this->regex) {
            throw new RuntimeException('Completion suggest accepts either prefix or regex, not both.');
        }

        $completion = ['field' => $this->field];

        foreach ([
            'size'            => $this->size,
            'skip_duplicates' => $this->skip_duplicates,
            'fuzzy'           => $this->fuzzy,
            'contexts'        => $this->contexts,
            'analyzer'        => $this->analyzer,
        ] as $key => $value) {
            if (null !== $value) {
                $completion[$key] = $value;
            }
        }

        $data = null !== $this->prefix ? ['prefix' => $this->prefix] : ['regex' => (string)$this->regex];
        $data['completion'] = $completion;

        return $data;
    }
}
