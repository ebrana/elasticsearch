<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Filters;

use Elasticsearch\Mapping\Exceptions\AttributeMissingException;
use Elasticsearch\Mapping\Settings\Filters\StopFilter;
use stdClass;

class StopFilterFactory implements FilterFactoryInterface
{
    /**
     * @throws \Elasticsearch\Mapping\Exceptions\AttributeMissingException
     */
    public static function create(string $name, stdClass $configuration): StopFilter
    {
        $stopwords_path = null;
        $ignore_case = false;
        $remove_trailing = true;

        if (!isset($configuration->stopwords)) {
            throw new AttributeMissingException('Stop filter must define stopwords.');
        }

        if (isset($configuration->stopwords_path)) {
            $stopwords_path = $configuration->stopwords_path;
        }

        if (isset($configuration->ignore_case)) {
            $ignore_case = (bool)$configuration->ignore_case;
        }

        if (isset($configuration->remove_trailing)) {
            $remove_trailing = (bool)$configuration->remove_trailing;
        }

        return new StopFilter($name, $configuration->stopwords, $stopwords_path, $ignore_case, $remove_trailing);
    }
}
