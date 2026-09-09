<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Filters\AsciiFoldingFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\EdgeNgramFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\ElisionFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\HunspellFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\KeywordMarkerFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\LengthFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\LowercaseFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\NgramFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\PatternReplaceFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\ShingleFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\StemmerFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\StopFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\SynonymFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\TrimFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\UniqueFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\WordDelimiterGraphFilterFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class FiltersResolver
{
    /** @var string[] */
    private array $filterFactories = [
        'stop'                 => StopFilterFactory::class,
        'ngram'                => NgramFilterFactory::class,
        'stemmer'              => StemmerFilterFactory::class,
        'edge_ngram'           => EdgeNgramFilterFactory::class,
        'hunspell'             => HunspellFilterFactory::class,
        'lowercase'            => LowercaseFilterFactory::class,
        'asciifolding'         => AsciiFoldingFilterFactory::class,
        'synonym'              => SynonymFilterFactory::class,
        'synonym_graph'        => SynonymFilterFactory::class,
        'word_delimiter_graph' => WordDelimiterGraphFilterFactory::class,
        'shingle'              => ShingleFilterFactory::class,
        'elision'              => ElisionFilterFactory::class,
        'keyword_marker'       => KeywordMarkerFilterFactory::class,
        'pattern_replace'      => PatternReplaceFilterFactory::class,
        'length'               => LengthFilterFactory::class,
        'unique'               => UniqueFilterFactory::class,
        'trim'                 => TrimFilterFactory::class,
    ];

    public function resolveFilters(stdClass $filters, Analysis $analysis): void
    {
        foreach ((array)$filters as $name => $filter) {
            if (isset($this->filterFactories[$filter->type])) {
                $factory = $this->filterFactories[$filter->type];
                $analysis->addFilter($factory::create($name, $filter));
            }
        }
    }
}
