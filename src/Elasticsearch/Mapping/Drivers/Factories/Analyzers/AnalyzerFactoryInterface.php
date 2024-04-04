<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Factories\Analyzers;

use Elasticsearch\Mapping\Settings\Analyzer;
use stdClass;

interface AnalyzerFactoryInterface
{
    public static function create(string $name, stdClass $configuration): Analyzer;
}
