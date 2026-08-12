<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

/**
 * Zaklad pro vestavene analyzery Elasticsearche (standard, simple, czech, ...).
 * Vlastni (custom) analyzer se skladany z tokenizeru a filtru resi tridou Analyzer.
 */
abstract class AbstractAnalyzer extends AbstractBase implements AnalyzerInterface
{
}
