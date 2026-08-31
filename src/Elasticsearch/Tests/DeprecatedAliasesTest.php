<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Mapping\Drivers\Factories\CharactedFilters\HtmlStripCharacterFilterFactory as DeprecatedHtmlStripFactory;
use Elasticsearch\Mapping\Drivers\Factories\CharacterFilters\HtmlStripCharacterFilterFactory;
use Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver\TokenizerResolver;
use Elasticsearch\Mapping\MappingMetada;
use Elasticsearch\Mapping\MappingMetadata;
use Elasticsearch\Mapping\Settings\AbstractCharactedFilter;
use Elasticsearch\Mapping\Settings\Analysis;
use Elasticsearch\Mapping\Settings\CharacterFilters\HtmlStripCharacterFilter;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Preklepy v API jsou opravene, ale stara jmena zustavaji jako deprecated aliasy.
 * Tenhle test hlida, ze porad fungujou.
 */
class DeprecatedAliasesTest extends TestCase
{
    public function testMappingMetadaExtendsMappingMetadata(): void
    {
        $old = new MappingMetada(new ArrayCollection([]));

        $this->assertInstanceOf(MappingMetadata::class, $old);
    }

    public function testCustomFilterOnDeprecatedBaseStillWorks(): void
    {
        // vlastni char filtr postaveny na starem zakladu musi projit i novou typovou kontrolou
        $filter = new class ('legacy') extends AbstractCharactedFilter {
            public function __construct(string $name)
            {
                parent::__construct($name, 'html_strip');
            }
        };

        $analysis = new Analysis();
        $analysis->addCharacterFilter($filter);

        $this->assertSame($filter, $analysis->getCharacterFilters()->get('legacy'));
    }

    public function testDeprecatedFactoryNamespaceStillProducesFilter(): void
    {
        $configuration = (object)['escaped_tags' => null];
        $filter = DeprecatedHtmlStripFactory::create('html', $configuration);

        $this->assertInstanceOf(HtmlStripCharacterFilter::class, $filter);
        $this->assertInstanceOf(HtmlStripCharacterFilterFactory::class, new DeprecatedHtmlStripFactory());
    }

    public function testDeprecatedResolveTokenizerMethodDelegates(): void
    {
        $tokenizers = json_decode('{"ngram_tok":{"type":"ngram","min_gram":2,"max_gram":3}}', false);
        $this->assertInstanceOf(stdClass::class, $tokenizers);

        $analysis = new Analysis();
        (new TokenizerResolver())->resolvetTokenizer($tokenizers, $analysis);

        $this->assertCount(1, $analysis->getTokenizers());
        $this->assertNotNull($analysis->getTokenizers()->get('ngram_tok'));
    }
}
