<?php

namespace CodeWithKyrian\FilamentDateRange\Forms\Components\StateCasts;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Carbon;

class DateRangeStateCast implements StateCast
{
    public function __construct(
        protected string $format,
        protected string $internalFormat,
        protected string $timezone,
    ) {
    }

    public function get(mixed $state): ?array
    {
        if (!is_array($state)) {
            return [
                'start' => null,
                'end' => null,
            ];
        }

        $start = $state['start'] ?? null;
        $end = $state['end'] ?? null;

        $start = $this->hydrate($start);
        $end = $this->hydrate($end);

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    public function set(mixed $state): ?array
    {
        if (!is_array($state)) {
            return null;
        }

        $start = $state['start'] ?? null;
        $end = $state['end'] ?? null;

        $start = $this->dehydrate($start);
        $end = $this->dehydrate($end);

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    protected function hydrate(mixed $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        if (!$date instanceof CarbonInterface) {
            $date = Carbon::parse($date);
        }

        $date->shiftTimezone($this->timezone);
        $date->setTimezone(config('app.timezone'));

        return $date->format($this->format);
    }

    protected function dehydrate(mixed $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        if (!$date instanceof CarbonInterface) {
            try {
                $date = Carbon::createFromFormat($this->format, (string) $date, config('app.timezone'));
            } catch (InvalidFormatException $exception) {
                try {
                    $date = Carbon::parse($date, config('app.timezone'));
                } catch (InvalidFormatException $exception) {
                    return null;
                }
            }
        }

        $date =  $date->setTimezone($this->timezone);

        return $date->format($this->internalFormat);
    }
}
