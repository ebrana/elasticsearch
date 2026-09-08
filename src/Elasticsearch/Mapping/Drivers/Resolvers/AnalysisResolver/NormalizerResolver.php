<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Drivers\Resolvers\AnalysisResolver;

use Elasticsearch\Mapping\Drivers\Factories\Normalizers\NormalizerFactory;
use Elasticsearch\Mapping\Settings\Analysis;
use stdClass;

final class NormalizerResolver
{
    public function resolveNormalizer(stdClass $normalizers, Analysis $analysis): void
    {
        /** @var stdClass $settings */
        foreach ((array)$normalizers as $name => $settings) {
            $analysis->addNormalizer(NormalizerFactory::create((string)$name, $settings));
        }
    }
}
