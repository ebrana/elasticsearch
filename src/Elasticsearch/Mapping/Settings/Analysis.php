<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;
use Doctrine\Common\Collections\ArrayCollection;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Analysis
{
    public function __construct(
        private ArrayCollection $analyzers = new ArrayCollection(),
        private ArrayCollection $filters = new ArrayCollection(),
        private ArrayCollection $tokenizers = new ArrayCollection(),
        private ArrayCollection $characterFilters = new ArrayCollection()
    ) {
    }

    public function addAnalyzer(Analyzer $analyzer): void
    {
        $this->analyzers->set($analyzer->getName(), $analyzer);
    }

    public function addFilter(AbstractFilter $filter): void
    {
        $this->filters->set($filter->getName(), $filter);
    }

    public function addCharacterFilter(AbstractCharactedFilter $filter): void
    {
        $this->characterFilters->set($filter->getName(), $filter);
    }

    public function addTokenizer(AbstractTokenizer $tokenizer): void
    {
        $this->tokenizers->set($tokenizer->getName(), $tokenizer);
    }

    public function getAnalyzers(): ArrayCollection
    {
        return $this->analyzers;
    }

    public function getFilters(): ArrayCollection
    {
        return $this->filters;
    }

    public function getCharacterFilters(): ArrayCollection
    {
        return $this->characterFilters;
    }

    public function getTokenizers(): ArrayCollection
    {
        return $this->tokenizers;
    }
}
