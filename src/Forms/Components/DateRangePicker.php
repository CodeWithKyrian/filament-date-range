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

    protected bool|Closure $autoApply = true;

    protected bool|Closure $dualCalendar = true;

    protected bool|Closure $singleField = false;

    protected array|bool|Closure|null $stacked = null;

    protected string|Htmlable|array|Closure|null $separatorContent = null;

    protected array|Closure|null $enabledDates = null;

    protected bool|Closure $hasTime = false;

    protected bool|Closure $allDayEnabled = false;

    protected bool|Closure $shouldInferAllDay = true;

    /**
     * Presets configuration.
     *
     * - `false` (default): presets are disabled.
     * - `true`: use the built-in preset ranges (Today, Yesterday, Last 7 Days, etc.).
     * - `array`: custom preset definitions, resolved at runtime.
     */
    protected bool|array|Closure $presets = false;

    protected string|Closure $defaultFormat = 'Y-m-d';

    protected string|Closure $defaultDisplayFormat = 'M j, Y';

    protected string|Closure $defaultFormatWithTime = 'Y-m-d H:i';

    protected string|Closure $defaultDisplayFormatWithTime = 'M j, Y H:i';

    protected function setUp(): void
    {
        parent::setUp();

        $this->stacked(false);

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

    public function separator(string|Htmlable|array|Closure|null $separator = 'to'): static
    {
        $this->separatorContent = $separator;
        return $this;
    }

    public function separatorIcon(string|BackedEnum|array|bool|null $icon = null): static
    {
        if (is_array($icon)) {
            $this->separator([
                'stacked' => $this->resolveSeparatorIcon($icon['stacked'] ?? null),
                'inline' => $this->resolveSeparatorIcon($icon['inline'] ?? null),
            ]);

            return $this;
        }

        $this->separator(function () use ($icon) {
            return $this->resolveSeparatorIcon($icon);
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

    /**
     * @deprecated Use autoApply() instead.
     */
    public function autoClose(bool|Closure $condition = true): static
    {
        return $this->autoApply($condition);
    }

    public function autoApply(bool|Closure $condition = true): static
    {
        $this->autoApply = $condition;

        return $this;
    }

    /**
     * Configure preset ranges for the calendar popover.
     *
     * When `$presets` is:
     * - `true`  => enable the built-in presets.
     * - `false` => disable presets.
     * - `array` => use a custom array of preset definitions.
     */
    public function presets(bool|array|Closure $presets = true): static
    {
        $this->presets = $presets;

        return $this;
    }

    public function dualCalendar(bool|Closure $condition = true): static
    {
        $this->dualCalendar = $condition;
        return $this;
    }

    /**
     * Render a single input showing the selected range, instead of two inputs.
     */
    public function singleField(bool|Closure $condition = true): static
    {
        $this->singleField = $condition;

        return $this;
    }

    public function withTime(bool|Closure $condition = true): static
    {
        $this->hasTime = $condition;

        return $this;
    }

    public function allDay(bool|Closure $enabled = true, bool|Closure $infer = true): static
    {
        $this->allDayEnabled = $enabled;
        $this->shouldInferAllDay = $infer;

        return $this;
    }

    public function inline(array|bool|Closure $condition = true): static
    {
        if ($condition instanceof Closure) {
            $this->stacked = function () use ($condition): array|bool|null {
                $value = $this->evaluate($condition);

                if (! is_array($value) && ! is_bool($value) && $value !== null) {
                    $value = null;
                }

                return $this->invertInlineValue($value);
            };

            return $this;
        }

        $this->stacked = $this->invertInlineValue($condition);

        return $this;
    }

    public function stacked(array|bool|Closure $condition = true): static
    {
        $this->stacked = $condition;

        return $this;
    }

    public function enabledDates(array|Closure|null $dates): static
    {
        $this->enabledDates = $dates;
        return $this;
    }

    public function getFormat(): string
    {
        return (string) ($this->evaluate($this->format)
            ?? ($this->hasTime()
                ? $this->evaluate($this->defaultFormatWithTime)
                : $this->evaluate($this->defaultFormat)));
    }

    public function getDisplayFormat(): string
    {
        return (string) ($this->evaluate($this->displayFormat)
            ?? ($this->hasTime()
                ? $this->evaluate($this->defaultDisplayFormatWithTime)
                : $this->evaluate($this->defaultDisplayFormat)));
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

    public function getSeparatorHtml(): string|Htmlable|array|null
    {
        $separator = $this->evaluate($this->separatorContent);

        if (! is_array($separator)) {
            return $separator;
        }

        $stacked = $separator['stacked'] ?? $separator['inline'] ?? null;
        $inline = $separator['inline'] ?? $separator['stacked'] ?? null;

        return [
            'stacked' => $stacked,
            'inline' => $inline,
        ];
    }

    protected function resolveSeparatorIcon(string|BackedEnum|bool|null $icon): ?Htmlable
    {
        if ($icon instanceof BackedEnum) {
            $icon = $icon->value;
        }

        if (! is_string($icon) || $icon === '') {
            return null;
        }

        return new HtmlString(Blade::render('<x-filament::icon icon="' . $icon . '" class="w-5 h-5" />'));
    }

    public function shouldShowPresets(): bool
    {
        $presets = $this->evaluate($this->presets);

        if ($presets === false || $presets === null) {
            return false;
        }

        return ! empty($this->getPresets());
    }

    /**
     * Resolve presets into a normalized array that the front-end can consume.
     *
     * Each preset will have the shape: ['key' => string, 'label' => string].
     *
     * Custom presets are limited to the known keys supported by the JS logic:
     * last_7_days, last_14_days, last_30_days, this_month, last_month, this_year, last_year.
     */
    public function getPresets(): array
    {
        $presets = $this->evaluate($this->presets);

        if ($presets === false || $presets === null) {
            return [];
        }

        // Use the built-in list.
        if ($presets === true) {
            return $this->getDefaultPresets();
        }

        if (! is_array($presets)) {
            throw new \InvalidArgumentException('DateRangePicker presets must be a boolean or an array.');
        }

        $allowedKeys = [
            'last_7_days',
            'last_14_days',
            'last_30_days',
            'this_month',
            'last_month',
            'this_year',
            'last_year',
        ];

        $normalized = [];

        foreach ($presets as $preset) {
            // Allow passing just the key string for convenience.
            if (is_string($preset)) {
                $preset = ['key' => $preset];
            }

            if (! is_array($preset) || ! isset($preset['key'])) {
                throw new \InvalidArgumentException('Each DateRangePicker preset must be an array with at least a "key" key.');
            }

            $key = $preset['key'];

            if (! in_array($key, $allowedKeys, true)) {
                throw new \InvalidArgumentException("Unsupported DateRangePicker preset key [{$key}].");
            }

            $normalized[] = [
                'key' => $key,
                'label' => $preset['label'] ?? $this->getDefaultPresetLabel($key),
            ];
        }

        return $normalized;
    }

    protected function getDefaultPresets(): array
    {
        $keys = [
            'last_7_days',
            'last_14_days',
            'last_30_days',
            'this_month',
            'last_month',
            'this_year',
            'last_year',
        ];

        return array_map(function (string $key): array {
            return [
                'key' => $key,
                'label' => $this->getDefaultPresetLabel($key),
            ];
        }, $keys);
    }

    protected function getDefaultPresetLabel(string $key): string
    {
        return match ($key) {
            'last_7_days' => __('filament-date-range::picker.presets.last_7_days'),
            'last_14_days' => __('filament-date-range::picker.presets.last_14_days'),
            'last_30_days' => __('filament-date-range::picker.presets.last_30_days'),
            'this_month' => __('filament-date-range::picker.presets.this_month'),
            'last_month' => __('filament-date-range::picker.presets.last_month'),
            'this_year' => __('filament-date-range::picker.presets.this_year'),
            'last_year' => __('filament-date-range::picker.presets.last_year'),
            default => $key,
        };
    }

    public function getStartPlaceholder(): ?string
    {
        return $this->evaluate($this->startPlaceholder) ?? __('filament-date-range::picker.placeholders.start_date', locale: $this->getLocale());
    }

    public function getEndPlaceholder(): ?string
    {
        return $this->evaluate($this->endPlaceholder) ?? __('filament-date-range::picker.placeholders.end_date', locale: $this->getLocale());
    }

    /**
     * @deprecated Use shouldAutoApply() instead.
     */
    public function shouldAutoClose(): bool
    {
        return $this->shouldAutoApply();
    }

    public function shouldAutoApply(): bool
    {
        return $this->evaluate($this->autoApply);
    }

    public function shouldDisplayDualCalendar(): bool
    {
        return $this->evaluate($this->dualCalendar);
    }

    public function isInline(): bool
    {
        $config = $this->getStackedConfig();

        return ! ($config['default'] ?? false);
    }

    public function getStackedClasses(): string
    {
        $config = $this->getStackedConfig();
        $classes = [];

        foreach (['default', 'sm', 'md', 'lg', 'xl', '2xl'] as $breakpoint) {
            $isStacked = $config[$breakpoint] ?? null;

            if ($isStacked === null) {
                continue;
            }

            $prefix = $breakpoint === 'default' ? 'default:' : "{$breakpoint}:";
            $classes[] = $prefix . ($isStacked ? 'stacked' : 'inline');
        }

        return implode(' ', $classes);
    }

    public function getStackedConfig(): array
    {
        $stacked = $this->evaluate($this->stacked);

        $defaults = [
            'default' => true,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        if ($stacked === null) {
            return $defaults;
        }

        if (is_bool($stacked)) {
            return [
                'default' => $stacked,
                'sm' => $stacked,
                'md' => $stacked,
                'lg' => $stacked,
                'xl' => $stacked,
                '2xl' => $stacked,
            ];
        }

        if (is_array($stacked)) {
            return array_merge($defaults, $stacked);
        }

        return $defaults;
    }

    protected function invertInlineValue(array|bool|null $inline): array|bool|null
    {
        if ($inline === null) {
            return null;
        }

        if (is_bool($inline)) {
            $isStacked = ! $inline;

            return [
                'default' => $isStacked,
                'sm' => $isStacked,
                'md' => $isStacked,
                'lg' => $isStacked,
                'xl' => $isStacked,
                '2xl' => $isStacked,
            ];
        }

        $inverted = [];

        foreach ($inline as $breakpoint => $value) {
            $inverted[$breakpoint] = is_bool($value) ? ! $value : $value;
        }

        return $inverted;
    }

    public function isSingleField(): bool
    {
        return $this->evaluate($this->singleField);
    }

    public function hasTime(): bool
    {
        return $this->evaluate($this->hasTime);
    }

    public function isAllDayEnabled(): bool
    {
        return $this->evaluate($this->allDayEnabled);
    }

    public function shouldInferAllDay(): bool
    {
        return $this->evaluate($this->shouldInferAllDay);
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
                } catch (\Exception $e) {
                }
            }
        }

        return empty($formattedDates) ? null : $formattedDates;
    }

    public function dehydrateValidationRules(array &$rules): void
    {
        parent::dehydrateValidationRules($rules);

        $statePath = $this->getStatePath();

        if (! $this->isRequired()) {
            return;
        }

        $label = $this->getLabel();

        $rules[$statePath] = [
            ...$rules[$statePath] ?? [],
            static function (string $attribute, mixed $value, Closure $fail) use ($label): void {
                $message = __('filament-date-range::picker.validation.both_required', [
                    'label' => $label,
                ]);

                if (! is_array($value)) {
                    $fail($message);
                    return;
                }

                $start = $value['start'] ?? null;
                $end = $value['end'] ?? null;

                if (blank($start) || blank($end)) {
                    $fail($message);
                }
            },
        ];
    }
}
