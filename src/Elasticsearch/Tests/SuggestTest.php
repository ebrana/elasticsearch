<?php

declare(strict_types=1);

namespace Elasticsearch\Tests;

use Elasticsearch\Search\Results\Result;
use Elasticsearch\Search\Suggest\CompletionSuggest;
use Elasticsearch\Search\Suggest\DirectGenerator;
use Elasticsearch\Search\Suggest\Enums\StringDistance;
use Elasticsearch\Search\Suggest\Enums\SuggestMode;
use Elasticsearch\Search\Suggest\Enums\SuggestSort;
use Elasticsearch\Search\Suggest\PhraseSuggest;
use Elasticsearch\Search\Suggest\Suggest;
use Elasticsearch\Search\Suggest\TermSuggest;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SuggestTest extends TestCase
{
    /**
     * @param array<string, mixed> $actual
     */
    private function assertJsonSame(string $expected, array $actual): void
    {
        $this->assertSame($expected, json_encode($actual, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    public function testTermSuggest(): void
    {
        $this->assertJsonSame(
            '{"opravy":{"text":"boyt","term":{"field":"name"}}}',
            (new Suggest(new TermSuggest('opravy', 'boyt', 'name')))->toArray()
        );

        $this->assertJsonSame(
            '{"opravy":{"text":"boyt","term":{"field":"name","size":3,"sort":"frequency",'
            . '"suggest_mode":"popular","max_edits":2,"min_doc_freq":1,'
            . '"string_distance":"jaro_winkler"}}}',
            (new Suggest(new TermSuggest(
                'opravy',
                'boyt',
                'name',
                size: 3,
                sort: SuggestSort::FREQUENCY,
                suggest_mode: SuggestMode::POPULAR,
                max_edits: 2,
                min_doc_freq: 1.0,
                string_distance: StringDistance::JARO_WINKLER
            )))->toArray()
        );
    }

    public function testPhraseSuggestWithDirectGenerator(): void
    {
        $this->assertJsonSame(
            '{"fraze":{"text":"cerne boyt","phrase":{"field":"name.trigram","size":1,"gram_size":3,'
            . '"confidence":0.5,"max_errors":2,"highlight":{"pre_tag":"<em>","post_tag":"</em>"},'
            . '"direct_generator":[{"field":"name.trigram","suggest_mode":"always","min_word_length":1}]}}}',
            (new Suggest(new PhraseSuggest(
                'fraze',
                'cerne boyt',
                'name.trigram',
                size: 1,
                gram_size: 3,
                confidence: 0.5,
                max_errors: 2,
                direct_generator: [
                    new DirectGenerator('name.trigram', suggest_mode: SuggestMode::ALWAYS, min_word_length: 1),
                ],
                highlight: ['pre_tag' => '<em>', 'post_tag' => '</em>']
            )))->toArray()
        );
    }

    public function testCompletionSuggestUsesPrefix(): void
    {
        $this->assertJsonSame(
            '{"doplneni":{"prefix":"bo","completion":{"field":"suggest","size":5,'
            . '"skip_duplicates":true,"fuzzy":{"fuzziness":"AUTO"}}}}',
            (new Suggest(new CompletionSuggest(
                'doplneni',
                'suggest',
                prefix: 'bo',
                size: 5,
                skip_duplicates: true,
                fuzzy: ['fuzziness' => 'AUTO']
            )))->toArray()
        );
    }

    public function testCompletionSuggestUsesRegex(): void
    {
        $this->assertJsonSame(
            '{"doplneni":{"regex":"bo.*","completion":{"field":"suggest"}}}',
            (new Suggest(new CompletionSuggest('doplneni', 'suggest', regex: 'bo.*')))->toArray()
        );
    }

    public function testCompletionSuggestRequiresPrefixOrRegex(): void
    {
        $this->expectException(RuntimeException::class);

        (new CompletionSuggest('doplneni', 'suggest'))->toArray();
    }

    public function testCompletionSuggestRejectsBothPrefixAndRegex(): void
    {
        $this->expectException(RuntimeException::class);

        (new CompletionSuggest('doplneni', 'suggest', prefix: 'bo', regex: 'bo.*'))->toArray();
    }

    public function testMultipleSuggestersAtOnce(): void
    {
        $suggest = new Suggest(new TermSuggest('opravy', 'boyt', 'name'));
        $suggest->add(new CompletionSuggest('doplneni', 'suggest', prefix: 'bo'));

        $this->assertCount(2, $suggest->getSuggesters());
        $this->assertJsonSame(
            '{"opravy":{"text":"boyt","term":{"field":"name"}},'
            . '"doplneni":{"prefix":"bo","completion":{"field":"suggest"}}}',
            $suggest->toArray()
        );
    }

    public function testSuggestRequiresAtLeastOneSuggester(): void
    {
        $this->expectException(RuntimeException::class);

        (new Suggest())->toArray();
    }

    public function testResultParsesSuggests(): void
    {
        $result = new Result([
            'suggest' => [
                'opravy' => [
                    [
                        'text'    => 'boyt',
                        'offset'  => 0,
                        'length'  => 4,
                        'options' => [
                            ['text' => 'boty', 'score' => 0.75, 'freq' => 3],
                            ['text' => 'body', 'score' => 0.5, 'freq' => 1],
                        ],
                    ],
                ],
                'doplneni' => [
                    [
                        'text'    => 'bo',
                        'offset'  => 0,
                        'length'  => 2,
                        'options' => [
                            ['text' => 'boty', '_id' => '1', '_index' => 'product', '_source' => ['name' => 'boty']],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(['opravy', 'doplneni'], array_keys($result->getSuggests()));

        $term = $result->getSuggest('opravy')[0];
        $this->assertSame('boyt', $term->getText());
        $this->assertSame(4, $term->getLength());
        $this->assertSame(['boty', 'body'], $term->getOptionTexts());

        $firstTermOption = $term->getFirstOption();
        $this->assertNotNull($firstTermOption);
        $this->assertSame(3, $firstTermOption->getFreq());
        $this->assertSame(0.75, $firstTermOption->getScore());

        $completion = $result->getSuggest('doplneni')[0]->getFirstOption();
        $this->assertNotNull($completion);
        $this->assertSame('1', $completion->getId());
        $this->assertSame('product', $completion->getIndex());
        $this->assertSame(['name' => 'boty'], $completion->getSource());
        // freq is returned by the term suggester only
        $this->assertNull($completion->getFreq());
    }

    public function testResultWithoutSuggestIsEmpty(): void
    {
        $result = new Result(['took' => 1]);

        $this->assertSame([], $result->getSuggests());
        $this->assertSame([], $result->getSuggest('opravy'));
    }
}
