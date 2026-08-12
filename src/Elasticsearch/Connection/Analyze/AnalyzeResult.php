<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Analyze;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, AnalyzeToken>
 */
final class AnalyzeResult implements IteratorAggregate, Countable
{
    /** @var AnalyzeToken[] */
    private array $tokens = [];

    /** @var array<string, mixed>|null */
    private ?array $detail = null;

    /**
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        if (isset($record['tokens']) && is_array($record['tokens'])) {
            foreach ($record['tokens'] as $token) {
                if (!is_array($token)) {
                    continue;
                }

                $this->tokens[] = new AnalyzeToken(
                    (string)($token['token'] ?? ''),
                    (int)($token['start_offset'] ?? 0),
                    (int)($token['end_offset'] ?? 0),
                    (string)($token['type'] ?? ''),
                    (int)($token['position'] ?? 0),
                );
            }
        }

        if (isset($record['detail']) && is_array($record['detail'])) {
            /** @var array<string, mixed> $detail */
            $detail = $record['detail'];
            $this->detail = $detail;
        }
    }

    /**
     * @return AnalyzeToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Jen textove hodnoty tokenu - typicky to, co clovek pri ladeni analyzeru chce videt.
     *
     * @return string[]
     */
    public function getTokenValues(): array
    {
        return array_map(static fn (AnalyzeToken $token): string => $token->getToken(), $this->tokens);
    }

    /**
     * Obsah `detail` z odpovedi, tedy vystup po jednotlivych krocich (jen pri explain: true).
     *
     * @return array<string, mixed>|null
     */
    public function getDetail(): ?array
    {
        return $this->detail;
    }

    public function getIterator(): ArrayIterator
    {
        /** @var ArrayIterator<int, AnalyzeToken> $iterator */
        $iterator = new ArrayIterator($this->tokens);

        return $iterator;
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
