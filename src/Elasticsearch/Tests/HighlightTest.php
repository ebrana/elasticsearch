<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Elasticsearch\Search\Highlight\Enums\BoundaryScanner;
use Elasticsearch\Search\Highlight\Enums\Encoder;
use Elasticsearch\Search\Highlight\Enums\HighlighterType;
use Elasticsearch\Search\Highlight\Enums\HighlightOrder;
use Elasticsearch\Search\Highlight\Highlight;
use Elasticsearch\Search\Highlight\HighlightField;
use Elasticsearch\Search\Queries\MatchQuery;
use Elasticsearch\Search\Results\HitsCollection;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class HighlightTest extends TestCase
{
    /**
     * @param array<string, mixed> $actual
     */
    private function assertJsonSame(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testFieldWithoutOptionsIsEmptyObject(): void
    {
        // {"fields":{"name":{}}} - an empty array would be rejected by ES
        $this->assertJsonSame(
            '{"fields":{"name":{}}}',
            (new Highlight(new HighlightField('name')))->toArray()
        );
    }

    public function testGlobalOptions(): void
    {
        $highlight = new Highlight(new HighlightField('name'));
        $highlight->setTags(['<em>'], ['</em>'])
            ->setType(HighlighterType::UNIFIED)
            ->setFragmentSize(120)
            ->setNumberOfFragments(3)
            ->setOrder(HighlightOrder::SCORE)
            ->setRequireFieldMatch(false)
            ->setBoundaryScanner(BoundaryScanner::SENTENCE)
            ->setNoMatchSize(80);
        $highlight->setEncoder(Encoder::HTML);

        $this->assertJsonSame(
            '{"type":"unified","pre_tags":["<em>"],"post_tags":["</em>"],"fragment_size":120,'
            . '"number_of_fragments":3,"order":"score","require_field_match":false,'
            . '"boundary_scanner":"sentence","no_match_size":80,"encoder":"html",'
            . '"fields":{"name":{}}}',
            $highlight->toArray()
        );
    }

    public function testPerFieldOptionsOverrideGlobal(): void
    {
        $description = new HighlightField('description');
        $description->setFragmentSize(300)->setNumberOfFragments(1);

        $highlight = new Highlight(new HighlightField('name'), $description);
        $highlight->setFragmentSize(100);

        $this->assertJsonSame(
            '{"fragment_size":100,"fields":{"name":{},"description":'
            . '{"fragment_size":300,"number_of_fragments":1}}}',
            $highlight->toArray()
        );
    }

    public function testHighlightQueryAndMatchedFields(): void
    {
        $field = new HighlightField('name');
        $field->setType(HighlighterType::FVH)
            ->setMatchedFields(['name', 'name.autocomplete'])
            ->setHighlightQuery(new MatchQuery('name', 'boty'));

        $this->assertJsonSame(
            '{"fields":{"name":{"type":"fvh","highlight_query":{"match":{"name":{"query":"boty",'
            . '"boost":1,"operator":"OR","auto_generate_synonyms_phrase_query":true,'
            . '"fuzzy_transpositions":true}}},"matched_fields":["name","name.autocomplete"]}}}',
            $highlight = (new Highlight($field))->toArray()
        );
    }

    public function testStyledTags(): void
    {
        $highlight = new Highlight(new HighlightField('name'));
        $highlight->useStyledTags();

        $this->assertJsonSame('{"tags_schema":"styled","fields":{"name":{}}}', $highlight->toArray());
    }

    public function testHighlightRequiresAtLeastOneField(): void
    {
        $this->expectException(RuntimeException::class);

        (new Highlight())->toArray();
    }

    public function testHitsCollectionExposesHighlights(): void
    {
        $hits = new HitsCollection(new ArrayCollection([
            ['_id' => '1', '_source' => [], 'highlight' => ['name' => ['cerne <em>boty</em>']]],
            ['_id' => '2', '_source' => []],
        ]));

        $this->assertSame(['1' => ['name' => ['cerne <em>boty</em>']]], $hits->getHighlights());
        // iterace zustava na surovych hitech
        $this->assertCount(2, $hits);
    }
}
