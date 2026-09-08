<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use BackedEnum;

abstract class AbstractParams
{
    /**
     * @return string[]
     */
    abstract protected function getParams(): array;

    /**
     * @return array<string, string|int|bool|null>
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->getParams() as $param) {
            $value = $this->$param;
            if (null !== $value) {
                if ($value instanceof BackedEnum) {
                    $value = $value->value;
                } elseif (is_object($value) && method_exists($value, 'toString')) {
                    // kept for objects that are not backed enums
                    $value = $value->toString();
                }
                /** @var string|int|bool|null $value */
                $result[$param] = $value;
            }
        }

        return $result;
    }
}
