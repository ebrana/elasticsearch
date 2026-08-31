<?php

declare(strict_types=1);

namespace Elasticsearch\Search\Aggregations\Composite;

class DateHistogramSource extends AbstractCompositeSource
{
    protected ?string $format = null;
    protected ?string $timeZone = null;

    /**
     * `$interval` je kalendarni jednotka (month, week, …) nebo pevny usek ("30d");
     * co z toho, urcuje `$calendar`.
     */
    public function __construct(
        string $name,
        string $field,
        private readonly string $interval,
        private readonly bool $calendar = true,
    ) {
        parent::__construct($name, $field);
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function timeZone(string $timeZone): static
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    protected function getType(): string
    {
        return 'date_histogram';
    }

    protected function provideSource(): array
    {
        $source = $this->calendar
            ? ['calendar_interval' => $this->interval]
            : ['fixed_interval' => $this->interval];

        if (null !== $this->format) {
            $source['format'] = $this->format;
        }

        if (null !== $this->timeZone) {
            $source['time_zone'] = $this->timeZone;
        }

        return $source;
    }
}
