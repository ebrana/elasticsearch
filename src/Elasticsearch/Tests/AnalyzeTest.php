<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Connection\Analyze\AnalyzeRequest;
use Elasticsearch\Connection\Analyze\AnalyzeResult;
use Elasticsearch\Mapping\Settings\Filters\Enums\Language;
use Elasticsearch\Mapping\Settings\Filters\StemmerFilter;
use PHPUnit\Framework\TestCase;

class AnalyzeTest extends TestCase
{
    public function testRequestBodyByAnalyzer(): void
    {
        $request = new AnalyzeRequest('12.34', analyzer: 'autocomplete_analyzer');

        $this->assertSame(
            ['text' => ['12.34'], 'analyzer' => 'autocomplete_analyzer'],
            $request->toArray()
        );
    }

    public function testRequestBodyByCustomComposition(): void
    {
        $request = new AnalyzeRequest(
            ['<b>12.34</b>', 'ABC'],
            tokenizer: 'whitespace',
            filter: ['lowercase'],
            charFilter: ['dots_replace_filter', 'html_strip'],
            explain: true
        );

        $this->assertSame([
            'text'        => ['<b>12.34</b>', 'ABC'],
            'tokenizer'   => 'whitespace',
            'filter'      => ['lowercase'],
            'char_filter' => ['dots_replace_filter', 'html_strip'],
            'explain'     => true,
        ], $request->toArray());
    }

    public function testRequestBodyWithInlineFilterDefinition(): void
    {
        // a filter can also be given as an inline definition, so it can be tested before it reaches an index
        $request = new AnalyzeRequest(
            'bilerne',
            tokenizer: 'standard',
            filter: [(new StemmerFilter('danish_stemmer', Language::DANISH))->toArray()]
        );

        $this->assertSame([
            'text'      => ['bilerne'],
            'tokenizer' => 'standard',
            'filter'    => [['type' => 'stemmer', 'language' => 'danish']],
        ], $request->toArray());
    }

    public function testResultParsesTokens(): void
    {
        $result = new AnalyzeResult([
            'tokens' => [
                ['token' => 'ab', 'start_offset' => 0, 'end_offset' => 2, 'type' => 'word', 'position' => 0],
                ['token' => 'cd', 'start_offset' => 3, 'end_offset' => 5, 'type' => 'word', 'position' => 1],
            ],
        ]);

        $this->assertCount(2, $result);
        $this->assertSame(['ab', 'cd'], $result->getTokenValues());
        $this->assertSame(3, $result->getTokens()[1]->getStartOffset());
        $this->assertNull($result->getDetail());
    }

    public function testResultParsesExplainDetail(): void
    {
        // with explain: true Elasticsearch returns a detailed breakdown in `detail` instead of `tokens`
        $result = new AnalyzeResult([
            'detail' => [
                'custom_analyzer' => true,
                'tokenizer'       => ['name' => 'whitespace', 'tokens' => [['token' => '12.34']]],
            ],
        ]);

        $this->assertCount(0, $result);
        $this->assertSame([], $result->getTokenValues());

        $detail = $result->getDetail();
        $this->assertIsArray($detail);
        $this->assertArrayHasKey('tokenizer', $detail);
        $this->assertSame(['name' => 'whitespace', 'tokens' => [['token' => '12.34']]], $detail['tokenizer']);
    }
}
