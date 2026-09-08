<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings\Analyzers\Enums;

/**
 * Languages for which Elasticsearch provides a built-in language analyzer.
 * The list is narrower than the stemmer filter one (Settings\Filters\Enums\Language), hence a separate enum.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lang-analyzer.html
 */
enum AnalyzerLanguage: string
{
    case ARABIC = 'arabic';
    case ARMENIAN = 'armenian';
    case BASQUE = 'basque';
    case BENGALI = 'bengali';
    case BRAZILIAN = 'brazilian';
    case BULGARIAN = 'bulgarian';
    case CATALAN = 'catalan';
    case CJK = 'cjk';
    case CZECH = 'czech';
    case DANISH = 'danish';
    case DUTCH = 'dutch';
    case ENGLISH = 'english';
    case ESTONIAN = 'estonian';
    case FINNISH = 'finnish';
    case FRENCH = 'french';
    case GALICIAN = 'galician';
    case GERMAN = 'german';
    case GREEK = 'greek';
    case HINDI = 'hindi';
    case HUNGARIAN = 'hungarian';
    case INDONESIAN = 'indonesian';
    case IRISH = 'irish';
    case ITALIAN = 'italian';
    case LATVIAN = 'latvian';
    case LITHUANIAN = 'lithuanian';
    case NORWEGIAN = 'norwegian';
    case PERSIAN = 'persian';
    case PORTUGUESE = 'portuguese';
    case ROMANIAN = 'romanian';
    case RUSSIAN = 'russian';
    case SERBIAN = 'serbian';
    case SORANI = 'sorani';
    case SPANISH = 'spanish';
    case SWEDISH = 'swedish';
    case TURKISH = 'turkish';
    case THAI = 'thai';
}
