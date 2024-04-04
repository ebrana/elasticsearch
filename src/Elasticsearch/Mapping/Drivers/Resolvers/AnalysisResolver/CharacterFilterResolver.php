<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\CharactedFilters\HtmlStripCharacterFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\CharactedFilters\MappingCharacterFilterFactory;
use Elasticsearch\Mapping\Drivers\Factories\CharactedFilters\PatternReplaceCharacterFilterFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class CharacterFilterResolver
{
    /** @var string[] */
    private array $filterFactories = [
        'pattern_replace' => PatternReplaceCharacterFilterFactory::class,
        'mapping'         => MappingCharacterFilterFactory::class,
        'html_strip'      => HtmlStripCharacterFilterFactory::class,
    ];

    public function resolveFilters(stdClass $filters, Analysis $analysis): void
    {
        foreach ((array)$filters as $name => $filter) {
            if (isset($this->filterFactories[$filter->type])) {
                $factory = $this->filterFactories[$filter->type];
                $analysis->addCharacterFilter($factory::create($name, $filter));
            }
        }
    }
}
