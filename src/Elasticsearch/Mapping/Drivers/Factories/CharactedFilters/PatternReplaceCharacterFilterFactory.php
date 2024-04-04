<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\CharactedFilters;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use Elasticsearch\Mapping\Settings\CharacterFilters\Enums\Flags;
use Elasticsearch\Mapping\Settings\CharacterFilters\PatternReplaceCharacterFilter;
use stdClass;

class PatternReplaceCharacterFilterFactory implements CharacterFilterFactoryInterface
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): AbstractCharactedFilter
    {
        if (!isset($configuration->pattern)) {
            throw new AttributeMissingException('Pattern Replace characted filter must define pattern.');
        }
        if (!isset($configuration->replacement)) {
            throw new AttributeMissingException('Pattern Replace characted filter must define replacement.');
        }

        $filter = new PatternReplaceCharacterFilter($name, $configuration->pattern, $configuration->replacement);

        if (isset($configuration->flags)) {
            foreach (explode('|', $configuration->flags) as $flag) {
                $flagEnum = match ($flag) {
                    'COMMENTS' => Flags::COMMENTS,
                    'CASE_INSENSITIVE' => Flags::CASE_INSENSITIVE,
                    default => null
                };
                if ($flagEnum) {
                    $filter->addFlag($flagEnum);
                }
            }
        }

        return $filter;
    }
}
