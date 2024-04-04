<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

enum OnScriptError: string
{
    case FAIL = 'fail';
    case CONTINUE = 'continue';
}
