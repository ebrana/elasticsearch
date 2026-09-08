<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

/**
 * Base for the built-in Elasticsearch analyzers (standard, simple, czech, ...).
 * A custom analyzer composed of a tokenizer and filters is handled by the Analyzer class.
 */
abstract class AbstractAnalyzer extends AbstractBase implements AnalyzerInterface
{
}
