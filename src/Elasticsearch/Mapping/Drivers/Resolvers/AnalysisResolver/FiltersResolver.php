<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Filters\EdgeNgramFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\NgramFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\StemmerFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\Filters\StopFilterFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class FiltersResolver
{
    /** @var string[] */
    private array $filterFactories = [
        'stop'       => StopFilterFactory::class,
        'ngram'      => NgramFilterFactory::class,
        'stemmer'    => StemmerFilterFactory::class,
        'edge_ngram' => EdgeNgramFilterFactory::class,
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
