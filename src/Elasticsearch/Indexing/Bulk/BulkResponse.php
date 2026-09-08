<?php

declare(strict_types=1);

namespace Elasticsearch\Indexing\Bulk;

use Countable;

/**
 * Bulk API response. Beware: bulk returns HTTP 200 even when some of the items fail - the errors
 * have to be checked via hasErrors() / getErrors().
 */
final class BulkResponse implements Countable
{
    private int $took;
    private bool $errors;
    private int $itemCount = 0;

    /** @var BulkItemError[] */
    private array $itemErrors = [];

    /**
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $this->took = (int)($record['took'] ?? 0);
        $this->errors = (bool)($record['errors'] ?? false);

        if (!isset($record['items']) || !is_array($record['items'])) {
            return;
        }

        foreach ($record['items'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $this->itemCount++;

            $action = (string)array_key_first($item);
            $result = $item[$action] ?? null;
            if (!is_array($result) || !isset($result['error'])) {
                continue;
            }

            /** @var array<string, mixed> $error */
            $error = is_array($result['error']) ? $result['error'] : [];

            $this->itemErrors[] = new BulkItemError(
                $action,
                (string)($result['_index'] ?? ''),
                isset($result['_id']) ? (string)$result['_id'] : null,
                (int)($result['status'] ?? 0),
                isset($error['type']) ? (string)$error['type'] : null,
                isset($error['reason']) ? (string)$error['reason'] : null,
            );
        }
    }

    public function getTook(): int
    {
        return $this->took;
    }

    public function hasErrors(): bool
    {
        return $this->errors || [] !== $this->itemErrors;
    }

    /**
     * @return BulkItemError[]
     */
    public function getErrors(): array
    {
        return $this->itemErrors;
    }

    public function getSuccessCount(): int
    {
        return $this->itemCount - count($this->itemErrors);
    }

    /**
     * The total number of items in the response.
     */
    public function count(): int
    {
        return max(0, $this->itemCount);
    }
}
