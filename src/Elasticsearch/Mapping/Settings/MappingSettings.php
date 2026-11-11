<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Settings;

use Attribute;
use InvalidArgumentException;

/**
 * Mapping limits of an index - the `index.mapping.*` settings
 * (https://www.elastic.co/docs/reference/elasticsearch/index-settings/mapping-limit).
 *
 * Only values that are set are sent. Elasticsearch rejects index creation with a setting its
 * version does not know, so every path carries the version it appeared in (PATHS / SINCE);
 * use unsupportedIn() to check a configuration against the version of the server.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class MappingSettings
{
    public const string TOTAL_FIELDS_LIMIT = 'total_fields.limit';
    public const string TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT = 'total_fields.ignore_dynamic_beyond_limit';
    public const string DEPTH_LIMIT = 'depth.limit';
    public const string NESTED_FIELDS_LIMIT = 'nested_fields.limit';
    public const string NESTED_PARENTS_LIMIT = 'nested_parents.limit';
    public const string NESTED_OBJECTS_LIMIT = 'nested_objects.limit';
    public const string FIELD_NAME_LENGTH_LIMIT = 'field_name_length.limit';
    public const string FIELD_NAME_LENGTH_IGNORE_DYNAMIC_BEYOND_LIMIT = 'field_name_length.ignore_dynamic_beyond_limit';
    public const string DIMENSION_FIELDS_LIMIT = 'dimension_fields.limit';

    /**
     * Known paths (below `index.mapping.`): the PHP type of the value and the Elasticsearch
     * version the setting appeared in. The order is the order in which the settings are serialized.
     *
     * @var array<string, array{type: 'int'|'bool', since: string}>
     */
    public const array PATHS = [
        self::TOTAL_FIELDS_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
        self::TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT => ['type' => 'bool', 'since' => '8.13.0'],
        self::DEPTH_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
        self::NESTED_FIELDS_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
        // not documented up to 8.19, present in the 9.x docs without a "since" badge
        self::NESTED_PARENTS_LIMIT => ['type' => 'int', 'since' => '9.0.0'],
        self::NESTED_OBJECTS_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
        self::FIELD_NAME_LENGTH_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
        self::FIELD_NAME_LENGTH_IGNORE_DYNAMIC_BEYOND_LIMIT => ['type' => 'bool', 'since' => '9.3.0'],
        self::DIMENSION_FIELDS_LIMIT => ['type' => 'int', 'since' => '8.0.0'],
    ];

    /** @var array<string, int|bool> */
    private array $values = [];

    /**
     * @param int|null  $total_fields_limit                            max. mappers (fields, objects, multi-fields, aliases, runtime fields); ES default 1000
     * @param bool|null $total_fields_ignore_dynamic_beyond_limit      ES 8.13+: dynamic fields above the limit end up in `_ignored` instead of failing the request
     * @param int|null  $depth_limit                                   max. depth of inner objects; ES default 20
     * @param int|null  $nested_fields_limit                           max. distinct `nested` mappings; ES default 50 (8.x), 100 (9.x)
     * @param int|null  $nested_parents_limit                          ES 9.x: max. nested fields that are parents of other nested fields; ES default 50
     * @param int|null  $nested_objects_limit                          max. nested JSON objects in one document; ES default 10000
     * @param int|null  $field_name_length_limit                       max. field name length; ES default unlimited
     * @param bool|null $field_name_length_ignore_dynamic_beyond_limit ES 9.3+: too long dynamic field names end up in `_ignored` instead of failing
     * @param int|null  $dimension_fields_limit                        max. time series dimensions (TSDS); ES default 32768
     */
    public function __construct(
        ?int $total_fields_limit = null,
        ?bool $total_fields_ignore_dynamic_beyond_limit = null,
        ?int $depth_limit = null,
        ?int $nested_fields_limit = null,
        ?int $nested_parents_limit = null,
        ?int $nested_objects_limit = null,
        ?int $field_name_length_limit = null,
        ?bool $field_name_length_ignore_dynamic_beyond_limit = null,
        ?int $dimension_fields_limit = null,
    ) {
        $this->set(self::TOTAL_FIELDS_LIMIT, $total_fields_limit);
        $this->set(self::TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT, $total_fields_ignore_dynamic_beyond_limit);
        $this->set(self::DEPTH_LIMIT, $depth_limit);
        $this->set(self::NESTED_FIELDS_LIMIT, $nested_fields_limit);
        $this->set(self::NESTED_PARENTS_LIMIT, $nested_parents_limit);
        $this->set(self::NESTED_OBJECTS_LIMIT, $nested_objects_limit);
        $this->set(self::FIELD_NAME_LENGTH_LIMIT, $field_name_length_limit);
        $this->set(self::FIELD_NAME_LENGTH_IGNORE_DYNAMIC_BEYOND_LIMIT, $field_name_length_ignore_dynamic_beyond_limit);
        $this->set(self::DIMENSION_FIELDS_LIMIT, $dimension_fields_limit);
    }

    /**
     * Sets a value by its path below `index.mapping.` (one of the PATHS constants); null unsets it.
     *
     * @throws InvalidArgumentException on an unknown path or a value of the wrong type
     */
    public function set(string $path, int|bool|null $value): void
    {
        if (!isset(self::PATHS[$path])) {
            throw new InvalidArgumentException(sprintf('Unknown mapping setting "index.mapping.%s".', $path));
        }
        if (null === $value) {
            unset($this->values[$path]);

            return;
        }
        if (get_debug_type($value) !== self::PATHS[$path]['type']) {
            throw new InvalidArgumentException(sprintf(
                'Mapping setting "index.mapping.%s" expects %s, %s given.',
                $path,
                self::PATHS[$path]['type'],
                get_debug_type($value)
            ));
        }
        $this->values[$path] = $value;
    }

    public function get(string $path): int|bool|null
    {
        return $this->values[$path] ?? null;
    }

    public function isEmpty(): bool
    {
        return [] === $this->values;
    }

    /**
     * Set values keyed by their path below `index.mapping.`, in the order of PATHS.
     *
     * @return array<string, int|bool>
     */
    public function toArray(): array
    {
        $result = [];
        foreach (self::PATHS as $path => $definition) {
            if (isset($this->values[$path])) {
                $result[$path] = $this->values[$path];
            }
        }

        return $result;
    }

    /**
     * Set values that the given Elasticsearch version (e.g. "8.12.3" from the server info)
     * does not know yet, keyed by path with the version they appeared in. Empty = safe to send.
     *
     * @return array<string, string>
     */
    public function unsupportedIn(string $esVersion): array
    {
        $unsupported = [];
        foreach ($this->toArray() as $path => $value) {
            $since = self::PATHS[$path]['since'];
            if (version_compare($esVersion, $since, '<')) {
                $unsupported[$path] = $since;
            }
        }

        return $unsupported;
    }

    public function getTotalFieldsLimit(): ?int
    {
        return $this->getInt(self::TOTAL_FIELDS_LIMIT);
    }

    public function setTotalFieldsLimit(?int $limit): void
    {
        $this->set(self::TOTAL_FIELDS_LIMIT, $limit);
    }

    public function getTotalFieldsIgnoreDynamicBeyondLimit(): ?bool
    {
        return $this->getBool(self::TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT);
    }

    public function setTotalFieldsIgnoreDynamicBeyondLimit(?bool $ignore): void
    {
        $this->set(self::TOTAL_FIELDS_IGNORE_DYNAMIC_BEYOND_LIMIT, $ignore);
    }

    public function getDepthLimit(): ?int
    {
        return $this->getInt(self::DEPTH_LIMIT);
    }

    public function setDepthLimit(?int $limit): void
    {
        $this->set(self::DEPTH_LIMIT, $limit);
    }

    public function getNestedFieldsLimit(): ?int
    {
        return $this->getInt(self::NESTED_FIELDS_LIMIT);
    }

    public function setNestedFieldsLimit(?int $limit): void
    {
        $this->set(self::NESTED_FIELDS_LIMIT, $limit);
    }

    public function getNestedParentsLimit(): ?int
    {
        return $this->getInt(self::NESTED_PARENTS_LIMIT);
    }

    public function setNestedParentsLimit(?int $limit): void
    {
        $this->set(self::NESTED_PARENTS_LIMIT, $limit);
    }

    public function getNestedObjectsLimit(): ?int
    {
        return $this->getInt(self::NESTED_OBJECTS_LIMIT);
    }

    public function setNestedObjectsLimit(?int $limit): void
    {
        $this->set(self::NESTED_OBJECTS_LIMIT, $limit);
    }

    public function getFieldNameLengthLimit(): ?int
    {
        return $this->getInt(self::FIELD_NAME_LENGTH_LIMIT);
    }

    public function setFieldNameLengthLimit(?int $limit): void
    {
        $this->set(self::FIELD_NAME_LENGTH_LIMIT, $limit);
    }

    public function getFieldNameLengthIgnoreDynamicBeyondLimit(): ?bool
    {
        return $this->getBool(self::FIELD_NAME_LENGTH_IGNORE_DYNAMIC_BEYOND_LIMIT);
    }

    public function setFieldNameLengthIgnoreDynamicBeyondLimit(?bool $ignore): void
    {
        $this->set(self::FIELD_NAME_LENGTH_IGNORE_DYNAMIC_BEYOND_LIMIT, $ignore);
    }

    public function getDimensionFieldsLimit(): ?int
    {
        return $this->getInt(self::DIMENSION_FIELDS_LIMIT);
    }

    public function setDimensionFieldsLimit(?int $limit): void
    {
        $this->set(self::DIMENSION_FIELDS_LIMIT, $limit);
    }

    private function getInt(string $path): ?int
    {
        $value = $this->values[$path] ?? null;

        return is_int($value) ? $value : null;
    }

    private function getBool(string $path): ?bool
    {
        $value = $this->values[$path] ?? null;

        return is_bool($value) ? $value : null;
    }
}
