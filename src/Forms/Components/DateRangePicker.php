<?php

namespace CodeWithKyrian\FilamentDateRange\Forms\Components;

use BackedEnum;
use Carbon\CarbonInterface;
use Closure;
use CodeWithKyrian\FilamentDateRange\Forms\Components\Concerns\HasStartEndAffixes;
use CodeWithKyrian\FilamentDateRange\Forms\Components\StateCasts\DateRangeStateCast;
use Filament\Forms\Components\Concerns\CanBeReadOnly;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class DateRangePicker extends Field
{
    use CanBeReadOnly;
    use HasStartEndAffixes;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament-date-range::forms.components.date-range-picker';

    protected string|Closure|null $displayFormat = null;

    protected string|Closure|null $format = null;

    protected CarbonInterface|string|Closure|null $minDate = null;

    protected CarbonInterface|string|Closure|null $maxDate = null;

    protected string|Closure|null $timezone = null;

    protected string|Closure|null $locale = null;

    protected int|Closure $firstDayOfWeek = 0;

    protected string|Closure|null $startPlaceholder = null;

    protected string|Closure|null $endPlaceholder = null;

    protected bool|Closure $autoClose = true;

    protected bool|Closure $dualCalendar = true;

    protected bool|Closure $isInline = true;

    protected array|Closure|null $enabledDates = null;

    protected string|Closure $defaultFormat = 'Y-m-d';

    protected string|Closure $defaultDisplayFormat = 'M j, Y';

    protected function setUp(): void
    {
        parent::setUp();

        $this->inline(true);

        $this->separator('to');
    }

    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(DateRangeStateCast::class, [
                'format' => $this->getFormat(),
                'internalFormat' => $this->getInternalFormat(),
                'timezone' => $this->getTimezone(),
            ]),
        ];
    }

    public function getInternalFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    public function defaultFormat(string|Closure $format): static
    {
        $this->defaultFormat = $format;
        return $this;
    }
    public function defaultDisplayFormat(string|Closure $format): static
    {
        $this->defaultDisplayFormat = $format;
        return $this;
    }

    public function displayFormat(string|Closure|null $format): static
    {
        $this->displayFormat = $format;
        return $this;
    }

    public function format(string|Closure $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function minDate(CarbonInterface|string|Closure|null $date): static
    {
        $this->minDate = $date;
        return $this;
    }

    public function maxDate(CarbonInterface|string|Closure|null $date): static
    {
        $this->maxDate = $date;
        return $this;
    }

    public function timezone(string|Closure|null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function locale(string|Closure|null $locale): static
    {
        $this->locale = $locale;
        return $this;
    }

    public function firstDayOfWeek(int|Closure $day): static
    {
        $this->firstDayOfWeek = $day;
        return $this;
    }

    public function weekStartsOnMonday(): static
    {
        $this->firstDayOfWeek(1);
        return $this;
    }

    public function weekStartsOnSunday(): static
    {
        $this->firstDayOfWeek(7);
        return $this;
    }

    public function separator(string|Htmlable|Closure|null $separator = 'to'): static
    {
        $this->separator = $separator;
        return $this;
    }

    public function separatorIcon(string|BackedEnum|bool|null $icon = null): static
    {
        $this->separator(static function () use ($icon) {
            $icon = $icon instanceof BackedEnum ? $icon->value : $icon;
            return new HtmlString(Blade::render('<x-filament::icon icon="' . $icon . '" class="w-5 h-5" />'));
        });

        return $this;
    }

    public function startPlaceholder(string|Closure|null $placeholder): static
    {
        $this->startPlaceholder = $placeholder;
        return $this;
    }

    public function endPlaceholder(string|Closure|null $placeholder): static
    {
        $this->endPlaceholder = $placeholder;
        return $this;
    }

    public function autoClose(bool|Closure $condition = true): static
    {
        $this->autoClose = $condition;
        return $this;
    }

    public function dualCalendar(bool|Closure $condition = true): static
    {
        $this->dualCalendar = $condition;
        return $this;
    }

    public function inline(bool|Closure $condition = true): static
    {
        $this->isInline = $condition;
        return $this;
    }

    public function stacked(bool|Closure $condition = true): static
    {
        $this->isInline = !$condition;
        return $this;
    }

    public function enabledDates(array|Closure|null $dates): static
    {
        $this->enabledDates = $dates;
        return $this;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format) ?? $this->evaluate($this->defaultFormat);
    }

    public function getDisplayFormat(): string
    {
        return $this->evaluate($this->displayFormat) ?? $this->evaluate($this->defaultDisplayFormat);
    }

    protected function getMinDateCarbon(): ?CarbonInterface
    {
        $date = $this->evaluate($this->minDate);

        if ($date instanceof CarbonInterface) {
            return $date->copy()->startOfDay();
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date)->startOfDay();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    protected function getMaxDateCarbon(): ?CarbonInterface
    {
        $date = $this->evaluate($this->maxDate);

        if ($date instanceof CarbonInterface) {
            return $date->copy()->startOfDay();
        }

        if (is_string($date)) {
            try {
                return Carbon::parse($date)->startOfDay();
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    public function getMinDate(): ?string
    {
        return $this->getMinDateCarbon()?->format('Y-m-d');
    }

    public function getMaxDate(): ?string
    {
        return $this->getMaxDateCarbon()?->format('Y-m-d');
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? FilamentTimezone::get();
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? config('app.locale');
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->evaluate($this->firstDayOfWeek);
    }

    public function getSeparatorHtml(): string|Htmlable|null
    {
        return $this->evaluate($this->separator);
    }

    public function getStartPlaceholder(): ?string
    {
        return $this->evaluate($this->startPlaceholder) ?? __('filament-date-range::picker.placeholders.start_date', locale: $this->getLocale());
    }

    public function getEndPlaceholder(): ?string
    {
        return $this->evaluate($this->endPlaceholder) ?? __('filament-date-range::picker.placeholders.end_date', locale: $this->getLocale());
    }

    public function shouldAutoClose(): bool
    {
        return $this->evaluate($this->autoClose);
    }

    public function shouldDisplayDualCalendar(): bool
    {
        return $this->evaluate($this->dualCalendar);
    }

    public function isInline(): bool
    {
        return $this->evaluate($this->isInline);
    }

    public function getEnabledDates(): ?array
    {
        $enabledDates = $this->evaluate($this->enabledDates);

        if ($enabledDates === null) {
            return null;
        }

        $formattedDates = [];
        
        foreach ($enabledDates as $date) {
            if ($date instanceof CarbonInterface) {
                $formattedDates[] = $date->format('Y-m-d');
                continue;
            }

            if (is_string($date)) {
                try {
                    $formattedDates[] = Carbon::parse($date)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        }

        return empty($formattedDates) ? null : $formattedDates;
    }
}
