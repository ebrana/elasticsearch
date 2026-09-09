<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Text;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Types\AbstractType;

/**
 * A field for autocomplete. Elasticsearch keeps an FST structure for it in memory, so it returns
 * suggestions very fast - it is queried through CompletionSuggest.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters.html#completion-suggester
 */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
final class CompletionType extends AbstractType
{
    public const int DEFAULT_MAX_INPUT_LENGTH = 50;

    /**
     * @param array<int, array<string, mixed>>|null $contexts allows filtering the suggestions
     *        by category or geo position
     */
    public function __construct(
        private ?string $analyzer = null,
        private ?string $search_analyzer = null,
        private bool $preserve_separators = true,
        private bool $preserve_position_increments = true,
        private int $max_input_length = self::DEFAULT_MAX_INPUT_LENGTH,
        private ?array $contexts = null,
        ?string $name = null,
        ?string $context = null,
    ) {
        parent::__construct();

        $this->context = $context;
        $this->type = 'completion';
        if (null !== $name && $name !== '') {
            $this->setName($name);
        }
    }

    public function getAnalyzer(): ?string
    {
        return $this->analyzer;
    }

    public function setAnalyzer(?string $analyzer): void
    {
        $this->analyzer = $analyzer;
    }

    public function getSearchAnalyzer(): ?string
    {
        return $this->search_analyzer;
    }

    public function setSearchAnalyzer(?string $search_analyzer): void
    {
        $this->search_analyzer = $search_analyzer;
    }

    public function isPreserveSeparators(): bool
    {
        return $this->preserve_separators;
    }

    public function setPreserveSeparators(bool $preserve_separators): void
    {
        $this->preserve_separators = $preserve_separators;
    }

    public function isPreservePositionIncrements(): bool
    {
        return $this->preserve_position_increments;
    }

    public function setPreservePositionIncrements(bool $preserve_position_increments): void
    {
        $this->preserve_position_increments = $preserve_position_increments;
    }

    public function getMaxInputLength(): int
    {
        return $this->max_input_length;
    }

    public function setMaxInputLength(int $max_input_length): void
    {
        $this->max_input_length = $max_input_length;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getContexts(): ?array
    {
        return $this->contexts;
    }

    /**
     * @param array<int, array<string, mixed>>|null $contexts
     */
    public function setContexts(?array $contexts): void
    {
        $this->contexts = $contexts;
    }

    public function getCollection(): ArrayCollection
    {
        $collection = parent::getCollection();

        if (null !== $this->analyzer) {
            $collection->set('analyzer', $this->analyzer);
        }

        if (null !== $this->search_analyzer) {
            $collection->set('search_analyzer', $this->search_analyzer);
        }

        if (false === $this->preserve_separators) {
            $collection->set('preserve_separators', false);
        }

        if (false === $this->preserve_position_increments) {
            $collection->set('preserve_position_increments', false);
        }

        if (self::DEFAULT_MAX_INPUT_LENGTH !== $this->max_input_length) {
            $collection->set('max_input_length', $this->max_input_length);
        }

        if (null !== $this->contexts) {
            $collection->set('contexts', $this->contexts);
        }

        return $collection;
    }
}
