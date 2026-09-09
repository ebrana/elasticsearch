<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Analyze;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, AnalyzeToken>
 */
final readonly class AnalyzeResult implements IteratorAggregate, Countable
{
    /** @var AnalyzeToken[] */
    private array $tokens;

    /** @var array<string, mixed>|null */
    private ?array $detail;

    /**
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $tokens = [];
        if (isset($record['tokens']) && is_array($record['tokens'])) {
            foreach ($record['tokens'] as $token) {
                if (!is_array($token)) {
                    continue;
                }

                $tokens[] = new AnalyzeToken(
                    (string)($token['token'] ?? ''),
                    (int)($token['start_offset'] ?? 0),
                    (int)($token['end_offset'] ?? 0),
                    (string)($token['type'] ?? ''),
                    (int)($token['position'] ?? 0),
                );
            }
        }
        $this->tokens = $tokens;

        $detail = null;
        if (isset($record['detail']) && is_array($record['detail'])) {
            /** @var array<string, mixed> $record['detail'] */
            $detail = $record['detail'];
        }
        $this->detail = $detail;
    }

    /**
     * @return AnalyzeToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Only the token strings - typically what one wants to see when debugging an analyzer.
     *
     * @return string[]
     */
    public function getTokenValues(): array
    {
        return array_map(static fn (AnalyzeToken $token): string => $token->getToken(), $this->tokens);
    }

    /**
     * The `detail` content of the response, i.e. the output of the individual steps (only with explain: true).
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
