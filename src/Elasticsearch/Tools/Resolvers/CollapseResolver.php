<?php

declare(strict_types=1);

namespace Elasticsearch\Tools\Resolvers;

class CollapseResolver
{
    use PhpValueResolverTrait;

    private int $counter = 0;

    /**
     * @param array<string, mixed> $collapse
     * @return string[]
     */
    public function resolve(array $collapse, string $target = '$builder'): array
    {
        if (!isset($collapse['field']) || !is_string($collapse['field'])) {
            throw new \RuntimeException('Collapse must contain field.');
        }

        $innerHitsDefinitions = $this->normalizeInnerHits($collapse['inner_hits'] ?? []);

        $collectionProperty = '$innerHitsCollection' . ++$this->counter;
        $collapseProperty = '$collapse' . $this->counter;
        $lines = [sprintf('%s = new InnerHitsCollection();', $collectionProperty)];

        foreach ($innerHitsDefinitions as $index => $innerHitsDefinition) {
            if (!is_array($innerHitsDefinition)) {
                continue;
            }

            $name = $innerHitsDefinition['name'] ?? ('innerHits' . $index);
            $size = isset($innerHitsDefinition['size']) ? (int) $innerHitsDefinition['size'] : 3;
            $collapseField = $innerHitsDefinition['collapse']['field'] ?? null;
            $from = isset($innerHitsDefinition['from']) ? (int) $innerHitsDefinition['from'] : null;
            $sort = $this->normalizeInnerHitsSort($innerHitsDefinition['sort'] ?? null);

            $innerHitsProperty = '$innerHits' . $this->counter . '_' . $index;
            $line = sprintf(
                '%s = new InnerHits(name: %s, size: %d',
                $innerHitsProperty,
                $this->resolvePhpValue($name),
                $size
            );
            if (is_string($collapseField)) {
                $line .= sprintf(', collapseField: %s', $this->resolvePhpValue($collapseField));
            }
            if (null !== $from) {
                $line .= sprintf(', from: %d', $from);
            }
            if (null !== $sort) {
                $line .= sprintf(', sort: %s', $this->resolvePhpValue($sort));
            }
            $line .= ');';
            $lines[] = $line;

            if (isset($innerHitsDefinition['_source'])) {
                $lines[] = sprintf('%s->setSource(%s);', $innerHitsProperty, $this->resolvePhpValue($innerHitsDefinition['_source']));
            }

            $lines[] = sprintf('%s->add(%s);', $collectionProperty, $innerHitsProperty);
        }

        $line = sprintf(
            '%s = new Collapse(field: %s, innerHits: %s',
            $collapseProperty,
            $this->resolvePhpValue($collapse['field']),
            $collectionProperty
        );
        if (isset($collapse['max_concurrent_group_searches'])) {
            $line .= sprintf(', max_concurrent_group_searches: %s', $this->resolvePhpValue((int) $collapse['max_concurrent_group_searches']));
        }
        $line .= ');';
        $lines[] = $line;
        $lines[] = sprintf('%s->setCollapse(%s);', $target, $collapseProperty);

        return $lines;
    }

    /**
     * @return array<int, mixed>
     */
    private function normalizeInnerHits(mixed $innerHits): array
    {
        if (!is_array($innerHits)) {
            return [];
        }

        return array_is_list($innerHits) ? $innerHits : [$innerHits];
    }

    /**
     * @return array<string, string>|null
     */
    private function normalizeInnerHitsSort(mixed $sort): ?array
    {
        if (!is_array($sort)) {
            return null;
        }

        $result = [];
        foreach ($sort as $item) {
            if (!is_array($item)) {
                continue;
            }

            $field = (string) array_key_first($item);
            $meta = $item[$field] ?? null;
            if (is_string($meta)) {
                $result[$field] = $meta;
                continue;
            }
            if (is_array($meta) && isset($meta['order']) && is_string($meta['order'])) {
                $result[$field] = $meta['order'];
            }
        }

        return [] === $result ? null : $result;
    }
}
