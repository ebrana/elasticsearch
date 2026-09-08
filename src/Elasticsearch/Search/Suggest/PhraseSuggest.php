<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Suggest;

/**
 * Suggestions for a whole phrase - it takes into account how often the words occur together.
 * The field should be analyzed with a shingle filter (e.g. "name.trigram").
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#phrase-suggester
 */
readonly class PhraseSuggest implements SuggestInterface
{
    /**
     * @param DirectGenerator[] $direct_generator
     * @param array{pre_tag: string, post_tag: string}|null $highlight
     * @param array<string, mixed>|null $collate
     */
    public function __construct(
        private string $name,
        private string $text,
        private string $field,
        private ?int $size = null,
        private ?int $gram_size = null,
        private ?float $real_word_error_likelihood = null,
        private ?float $confidence = null,
        private float|int|string|null $max_errors = null,
        private ?string $separator = null,
        private ?string $analyzer = null,
        private ?int $shard_size = null,
        private array $direct_generator = [],
        private ?array $highlight = null,
        private ?array $collate = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $phrase = ['field' => $this->field];

        $options = [
            'size'                       => $this->size,
            'gram_size'                  => $this->gram_size,
            'real_word_error_likelihood' => $this->real_word_error_likelihood,
            'confidence'                 => $this->confidence,
            'max_errors'                 => $this->max_errors,
            'separator'                  => $this->separator,
            'analyzer'                   => $this->analyzer,
            'shard_size'                 => $this->shard_size,
            'highlight'                  => $this->highlight,
            'collate'                    => $this->collate,
        ];

        foreach ($options as $key => $value) {
            if (null !== $value) {
                $phrase[$key] = $value;
            }
        }

        if ($this->direct_generator) {
            $phrase['direct_generator'] = array_map(
                static fn (DirectGenerator $generator): array => $generator->toArray(),
                $this->direct_generator
            );
        }

        return [
            'text'   => $this->text,
            'phrase' => $phrase,
        ];
    }
}
