<?php

declare(strict_types=1);

namespace Elasticsearch\Connection\Params;

use Generator;

abstract class AbstractParams
{
    abstract protected function getParams(): Generator;

    /**
     * @return array<string, string|int|null>
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->getParams() as $param) {
            $value = $this->$param;
            if (null !== $value) {
                if (is_object($value) && method_exists($value, 'toString')) {
                    $value = $value->toString();
                }
                $result[$param] = $value;
            }
        }

        return $result;
    }
}
