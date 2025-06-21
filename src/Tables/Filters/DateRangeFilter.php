<?php

namespace CodeWithKyrian\FilamentDateRange\Tables\Filters;

use Carbon\CarbonInterface;
use Closure;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

class DateRangeFilter extends BaseFilter
{
    protected string | Closure | null $displayFormat = null;

    protected string | Closure | null $format = null;

    protected CarbonInterface | string | Closure | null $minDate = null;

    protected CarbonInterface | string | Closure | null $maxDate = null;

    protected string | Closure | null $timezone = null;

    protected string | Closure | null $locale = null;

    protected int | Closure $firstDayOfWeek = 0;

    protected string | Closure | null $startPlaceholder = null;

    protected string | Closure | null $endPlaceholder = null;

    protected bool | Closure $autoClose = true;

    protected bool | Closure $dualCalendar = true;

    protected bool | Closure $isInline = true;

    protected string | null $startColumn = null;

    protected string | null $endColumn = null;

    protected bool | Closure $isLabelHidden = false;


    protected function setUp(): void
    {
        parent::setUp();

        $this->startPlaceholder(__('filament-date-range::picker.placeholders.start_date'));
        $this->endPlaceholder(__('filament-date-range::picker.placeholders.end_date'));

        $this->indicateUsing(function (array $data): ?string {
            $state = $data[$this->getName()] ?? null;

            $start = $state['start'] ?? null;
            $end = $state['end'] ?? null;

            if (!$start && !$end) {
                return null;
            }

            $label = $this->getLabel();

            if ($start && !$end) {
                $start = Carbon::parse($start)->translatedFormat($this->getDisplayFormatForIndicator());
                return "{$label}: " . __('filament-date-range::picker.filters.from') . " {$start}";
            }

            if (!$start && $end) {
                $end = Carbon::parse($end)->translatedFormat($this->getDisplayFormatForIndicator());
                return "{$label}: " . __('filament-date-range::picker.filters.until') . " {$end}";
            }

            $start = Carbon::parse($start)->translatedFormat($this->getDisplayFormatForIndicator());
            $end = Carbon::parse($end)->translatedFormat($this->getDisplayFormatForIndicator());

            return "{$label}: {$start} - {$end}";
        });

        $this->form([
            DateRangePicker::make($this->getName())
                ->label($this->getLabel())
                ->hiddenLabel($this->isLabelHidden())
                ->displayFormat($this->getDisplayFormat())
                ->format($this->getFormat())
                ->minDate($this->getMinDate())
                ->maxDate($this->getMaxDate())
                ->timezone($this->getTimezone())
                ->locale($this->getLocale())
                ->firstDayOfWeek($this->getFirstDayOfWeek())
                ->startPlaceholder($this->getStartPlaceholder())
                ->endPlaceholder($this->getEndPlaceholder())
                ->autoClose($this->shouldAutoClose())
                ->dualCalendar($this->shouldDisplayDualCalendar())
                ->inline($this->isInline())
        ]);
    }

    public function apply(Builder $query, array $data = []): Builder
    {
        $state = $data[$this->getName()] ?? null;

        $start = $state['start'] ?? null;
        $end = $state['end'] ?? null;

        if (!$start && !$end) {
            return $query;
        }

        $start = $start ? Carbon::parse($start)->setTimezone($this->getTimezone())->startOfDay() : null;
        $end = $end ? Carbon::parse($end)->setTimezone($this->getTimezone())->endOfDay() : null;

        $column = $this->getFilterColumnName();

        if ($this->hasQueryModificationCallback()) {
            $this->evaluate($this->modifyQueryUsing, [
                'data' => $data,
                'query' => $query,
                'state' => $state,
                'start' => $start,
                'end' => $end,
            ]);

            return $query;
        }

        return $query
            ->when($start, fn(Builder $query, $date) => $query->where($column, '>=', $date))
            ->when($end, fn(Builder $query, $date) => $query->where($column, '<=', $date));
    }

    /**
     * Determines the database column to filter against.
     * If startColumn and endColumn are set, it implies a range across two DB fields (less common for this filter type).
     * Typically, this filter applies to a single timestamp/date column where records fall within the range.
     */
    protected function getFilterColumnName(): string
    {
        return $this->getName();
    }

    public function displayFormat(string | Closure | null $format): static
    {
        $this->displayFormat = $format;
        return $this;
    }

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function minDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->minDate = $date;
        return $this;
    }

    public function maxDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->maxDate = $date;
        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function locale(string | Closure | null $locale): static
    {
        $this->locale = $locale;
        return $this;
    }

    public function firstDayOfWeek(int | Closure $day): static
    {
        $this->firstDayOfWeek = $day;
        return $this;
    }

    public function startPlaceholder(string | Closure | null $placeholder): static
    {
        $this->startPlaceholder = $placeholder;
        return $this;
    }

    public function endPlaceholder(string | Closure | null $placeholder): static
    {
        $this->endPlaceholder = $placeholder;
        return $this;
    }

    public function dualCalendar(bool | Closure $condition = true): static
    {
        $this->dualCalendar = $condition;
        return $this;
    }

    public function autoClose(bool | Closure $condition = true): static
    {
        $this->autoClose = $condition;
        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;
        return $this;
    }

    public function stacked(bool | Closure $condition = true): static
    {
        $this->isInline = ! $condition;
        return $this;
    }

    public function hiddenLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;
        return $this;
    }

    public function getDisplayFormat(): string
    {
        return $this->evaluate($this->displayFormat) ?? DateRangePicker::$defaultDisplayFormat;
    }

    public function getDisplayFormatForIndicator(): string // Can be different if needed
    {
        return $this->evaluate($this->displayFormat) ?? DateRangePicker::$defaultDisplayFormat;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format) ?? DateRangePicker::$defaultFormat;
    }

    public function getMinDate(): CarbonInterface | string | Closure | null
    {
        return $this->evaluate($this->minDate);
    }

    public function getMaxDate(): CarbonInterface | string | Closure | null
    {
        return $this->evaluate($this->maxDate);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? config('app.locale');
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->evaluate($this->firstDayOfWeek);
    }

    public function getStartPlaceholder(): ?string
    {
        return $this->evaluate($this->startPlaceholder);
    }

    public function getEndPlaceholder(): ?string
    {
        return $this->evaluate($this->endPlaceholder);
    }

    public function shouldDisplayDualCalendar(): bool
    {
        return $this->evaluate($this->dualCalendar);
    }

    public function shouldAutoClose(): bool
    {
        return $this->evaluate($this->autoClose);
    }

    public function isLabelHidden(): bool
    {
        return $this->evaluate($this->isLabelHidden);
    }

    public function isInline(): bool
    {
        return $this->evaluate($this->isInline);
    }
}
