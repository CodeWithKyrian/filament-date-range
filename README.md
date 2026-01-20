# Filament DateRange

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codewithkyrian/filament-date-range.svg?style=flat-square)](https://packagist.org/packages/codewithkyrian/filament-date-range)
[![Total Downloads](https://img.shields.io/packagist/dt/codewithkyrian/filament-date-range.svg?style=flat-square)](https://packagist.org/packages/codewithkyrian/filament-date-range)
[![License](https://img.shields.io/packagist/l/codewithkyrian/filament-date-range.svg?style=flat-square)](https://packagist.org/packages/codewithkyrian/filament-date-range)

**Filament DateRange** is a sleek and lightweight plugin, built purely ontop AlpineJs that provides a date range picker and filter for the [Filament](https://filamentphp.com/) panel.

It has a remarkably small footprint (JS ~105KB, gzipped ~26.2KB), ensuring your admin panel remains fast and responsive without sacrificing functionality or a polished look and feel.

![Filament DateRange Hero Image](art/showcase-main.png)

## Features

*   **Intuitive Date Range Picker Form Field:** Allows users to easily select "start" and "end" dates.
*   **Powerful Table Filter:** Seamlessly filter your table records by date ranges.
*   **Quick Preset Ranges:** Optional sidebar with common ranges like Today, Yesterday, Last 7 Days, This Month, etc.
*   **Single Field Mode:** Optionally render a single input that displays the full date range (e.g. for compact table filters).
*   **Time Range Support:** Optional time selection with an all-day toggle that defaults to start/end of day.
*   **Lightweight & Performant:** Pure AlpineJS implementation with a minimal asset footprint.
*   **Highly Customizable:** Extensive API for tailoring appearance, behavior, date formats, locales, and more.
*   **RTL Support:** Automatically adjusts for right-to-left languages.
*   **Localization Ready:** Comes with translations for many common locales.
*   **Seamless Filament Integration:** Designed to look and feel native to the Filament ecosystem.

## Version Compatibility

| Filament | Filament DateRange |
| :--- | :--- |
| 3.x | 1.x |
| 4.x | 2.x |
| 5.x | 2.1+ |

## Installation

You can install the package via Composer:

```bash
composer require codewithkyrian/filament-date-range
```

If you wish to customize the translations, you can publish them using:

```bash
php artisan vendor:publish --tag="filament-date-range-translations"
```

Similarly, you can also publish the views if you wish using:

```bash
php artisan vendor:publish --tag="filament-date-range-views"
```

## Form Field: `DateRangePicker`

The `DateRangePicker` component provides an elegant way to select a date range within your Filament forms.

![The DateRangePicker field in its default empty state, showing placeholders.](art/form-field-basic-empty.png)

### Basic Usage

To use the field, simply add it to your form schema:

```php
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

DateRangePicker::make('event_period')
                ->label('Event Period'),
```

![The DateRangePicker field with a date range selected, popover closed.](art/form-field-basic-selected.png)

The field internally manages its state as an array with `start` and `end` keys, for example: `['start' => '2024-01-15', 'end' => '2024-01-20']`. How this state is persisted depends on your model and database structure.

#### Storing as a Single JSON Attribute

If you intend to store the date range in a single database column (e.g., a `json` column named `event_period`), ensure your Eloquent model casts this attribute to an `array` or `json`:

```php
protected $casts = [
    'event_period' => 'json',
];
```

#### Storing in Separate Date Columns

If you prefer to store the start and end dates in separate database columns (e.g., `starts_at` and `ends_at`), the recommended approach is to define an Eloquent Accessor & Mutator on your model for a virtual attribute.

```php
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

protected function eventPeriod(): Attribute
{
    return Attribute::make(
        get: fn ($value, array $attributes) => [
            'start' => $attributes['starts_at'] ?? null,
            'end'   => $attributes['ends_at'] ?? null,
        ],
        set: fn (?array $value) => [
            'starts_at' => $value['start'] ? Carbon::parse($value['start']) : null,
            'ends_at'   => $value['end'] ? Carbon::parse($value['end']) : null,
        ],
    );
}

// To ensure the `event_period` virtual attribute is correctly handled during mass assignment operations (e.g., `Model::create([])` or `Model::update([])`),
// add its name to the `$fillable` array. This allows the `set` accessor to process the incoming date range.
protected $fillable = [
    // other fillables
    'event_period'
];


// To ensure your `event_period` attribute is available when Filament populates
// the form for an existing record, you may need to add it to the `$appends` array on your model:
protected $appends = [
    'event_period',
];
```

```php
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

DateRangePicker::make('event_period')
    ->label('Event Period')
```

### Customization Options

#### `displayFormat(string | Closure | null $format)`

Sets the display format for dates in the input fields. Accepts a **PHP date format** string. Defaults to `M j, Y` (e.g., "Jan 15, 2024"). See [PHP date formats](https://www.php.net/manual/en/function.date.php).
```php
DateRangePicker::make('booking_dates')
    ->displayFormat('d/m/Y') // Displays as "15/01/2024"
```
![Input fields showing dates like "15/01/2024".](art/form-field-displayformat.png)

#### `format(string | Closure $format)`

Sets the underlying storage format for dates (used for component state and database interaction). Accepts a **PHP date format** string. Defaults to `Y-m-d` (e.g., "2024-01-15"). See [PHP date formats](https://www.php.net/manual/en/function.date.php).
```php
DateRangePicker::make('log_period')
    ->format('Y-m-d H:i:s') // Stores as "2024-01-15 10:30:00"
```

#### `defaultFormat(string | Closure $format)`

Sets the default storage format for all instances of the `DateRangePicker`. This is useful if you want to set a global format for your application.

```php
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

DateRangePicker::configureUsing(function (DateRangePicker $picker) {
    $picker->defaultFormat('Y-m-d H:i:s');
});
```

#### `defaultDisplayFormat(string | Closure $format)`

Sets the default display format for all instances of the `DateRangePicker`.

```php
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

DateRangePicker::configureUsing(function (DateRangePicker $picker) {
    $picker->defaultDisplayFormat('d/m/Y');
});
```

#### `minDate(CarbonInterface | string | Closure | null $date)`

Sets the earliest selectable date. Dates before this will be disabled in the calendar.
```php
DateRangePicker::make('future_event')
    ->minDate(now()->addDay())
```
![Calendar popover showing past dates and today disabled.](art/form-field-mindate.png)

#### `maxDate(CarbonInterface | string | Closure | null $date)`

Sets the latest selectable date. Dates after this will be disabled.
```php
DateRangePicker::make('past_event')
    ->maxDate(now()->subDay())
```
![Calendar popover showing future dates and today disabled.](art/form-field-maxdate.png)

#### `enabledDates(array | Closure | null $dates)`

Sets specific enabled dates. When provided, only the dates in this array will be selectable; all other dates will be disabled. Accepts an array of dates (as Carbon instances or strings) or a closure that returns such an array.
```php
DateRangePicker::make('available_dates')
    ->enabledDates([
        '2024-01-15',
        '2024-01-16', 
        '2024-01-20',
        now()->addDays(5),
    ])
```

![Date range picker with specific enabled dates showing only selectable dates highlighted](art/form-field-enabled-dates.png)

#### `locale(string | Closure | null $locale)`

Sets the locale for the calendar (month names, day names) and potentially for parsing/displaying dates if the format string is locale-aware. Defaults to your application's current locale.
```php
DateRangePicker::make('période')
    ->locale('fr')
```
![Calendar popover showing month/day names in French.](art/form-field-locale-fr.png)

#### `timezone(string | Closure | null $timezone)`

Specifies the timezone to be used for interpreting and processing dates. Defaults to your application's timezone (`config('app.timezone')`).
```php
DateRangePicker::make('appointment_time')
    ->timezone('America/New_York')
```

#### `firstDayOfWeek(int | Closure $day)`

Sets the first day of the week in the calendar view. `0` for Sunday (default), `1` for Monday, ..., `6` for Saturday.
```php
DateRangePicker::make('work_schedule')
    ->firstDayOfWeek(1) // Week starts on Monday
```
![Calendar popover with Monday as the first column.](art/form-field-firstday-monday.png)

#### `weekStartsOnMonday()`

Convenience method to set the first day of the week to Monday.

```php
DateRangePicker::make('work_schedule')
    ->weekStartsOnMonday()
```

#### `weekStartsOnSunday()`

Convenience method to set the first day of the week to Sunday.

```php
DateRangePicker::make('work_schedule')
    ->weekStartsOnSunday()
```

#### `withTime(bool | Closure $condition = true)`

Enables time selection alongside the date range. When enabled, the picker defaults to **start of day** for the start date and **end of day** for the end date.

```php
DateRangePicker::make('maintenance_window')
    ->withTime()
    ->helperText('Defaults to all-day; toggle off to pick specific times.');
```

![Time-enabled date range picker with time inputs.](art/form-field-time-range.png)

#### `allDay(bool | Closure $enabled = true, bool | Closure $infer = true)`

Enables all-day functionality when time selection is enabled. When `$enabled` is `true`, the all-day toggle is shown. When `$infer` is `true`, the component infers the all-day state from values (including defaults), turning the toggle on when the start time is at the start of day and the end time is at the end of day.

```php
DateRangePicker::make('maintenance_window')
    ->withTime()
    ->allDay(true, true);
```

![Time-enabled date range picker showing the all-day toggle.](art/form-field-time-range-toggle.png)

#### `startPlaceholder(string | Closure | null $placeholder)`

Sets the placeholder text for the "Start" date input field. Defaults to a localized "Start Date".
```php
DateRangePicker::make('travel_itenery')
    ->startPlaceholder('Departure Date')
```

#### `endPlaceholder(string | Closure | null $placeholder)`

Sets the placeholder text for the "End" date input field. Defaults to a localized "End Date".
```php
DateRangePicker::make('travel_itenery')
    ->endPlaceholder('Return Date')
```
![Inputs showing "Departure Date" and "Return Date".](art/form-field-placeholders.png)

#### `separator(string | Htmlable | Closure $separator)`

Customizes the text or HTML displayed between the "Start" and "End" input fields. Defaults to "to".
```php
DateRangePicker::make('duration')
    ->separator('through')
```

#### `separatorIcon(string | Closure | null $icon)`

A convenience method to use a Filament icon as the separator.
```php
DateRangePicker::make('project_duration')
    ->separatorIcon('heroicon-o-arrow-long-right')
```
![An arrow icon shown between the "Start" and "End" inputs.](art/form-field-separator-icon.png)

#### `autoClose(bool | Closure $condition = true)`

If `true` (default), the calendar popover will close immediately after a valid date range (both start and end dates) is selected. If `false`, "Apply" and "Cancel" buttons are shown in the popover, requiring an explicit click to confirm the selection and close. When `withTime()` is enabled, auto-close is disabled automatically so users can adjust times before applying.
```php
DateRangePicker::make('conference_dates')
    ->autoClose(true)
```
![Calendar popover with "Apply" and "Cancel" buttons visible.](art/form-field-no-autoclose.png)

#### `presets(bool | array| Closure $condition = true)`

Enables a presets sidebar in the calendar popover with common ranges such as **Last 7 Days**, **Last 2 Weeks**, **Last 30 Days**, **This Month**, **Last Month**, **This Year**, and **Last Year**.  
You can pass:
- `true` (default when calling `->presets()` with no arguments) to use the built-in list.
- `false` to disable presets.
- an `array` (or `Closure` that returns an array) of preset definitions to customize which presets are shown.

```php
DateRangePicker::make('order_period')
    ->label('Order Period')
    ->presets(); // use built-in presets

DateRangePicker::make('order_period_custom')
    ->label('Order Period')
    ->presets([
        // Just keys (uses built-in labels)
        'last_7_days',
        'last_14_days',

        // Or explicit key + custom label
        ['key' => 'this_month', 'label' => 'Current Month'],
    ]);
```

![Date range picker showing a presets sidebar with common ranges like Last 7 Days and Last 2 Weeks.](art/form-field-presets.png)

#### `dualCalendar(bool | Closure $condition = true)`

Determines if two calendars (for consecutive months) should be displayed side-by-side in the popover. Defaults to `true` (dual calendars). Set to `false` to display a single calendar.
```php
DateRangePicker::make('date_range')
    ->dualCalendar(false) // Show only a single month calendar
```
![Calendar popover showing only one month.](art/form-field-single-calendar.png)

#### `inline(bool | Closure $condition = true)`

Controls whether the start and end inputs are displayed inline (horizontally) or stacked (vertically). Defaults to `true` (inline layout).
```php
DateRangePicker::make('event_period')
    ->inline() // Display inputs side by side (default)
```
![Date range picker with inputs displayed horizontally side by side.](art/form-field-inline.png)

#### `stacked(bool | Closure $condition = true)`

A convenience method to set the layout to stacked (vertical). Equivalent to `->inline(false)`.
```php
DateRangePicker::make('event_period')
    ->stacked() // Display inputs vertically stacked
```
![Date range picker with inputs displayed vertically stacked.](art/form-field-stacked.png)

#### `singleField(bool | Closure $condition = true)`

Renders a single input that displays the entire date range (for example: `Jan 3, 2026 — Jan 8, 2026`) instead of separate start and end inputs. This works especially well for compact layouts.

```php
DateRangePicker::make('invoice_period')
    ->label('Invoice Period')
    ->singleField();
```

![Single input field displaying the full selected date range.](art/form-field-single-field.png)

#### `readOnly(bool | Closure $condition = true)`

Makes the input fields read-only, preventing direct text input (selection via calendar is still possible).

#### `disabled(bool | Closure $condition = true)`

Disables the entire component, preventing any interaction.

#### `required(bool | Closure $condition = true)`

When required, both the start and end dates must be filled. If only one side is selected, validation will fail for the missing side.

#### Affixes (Prefixes & Suffixes)

You can add icons, labels, or actions as prefixes or suffixes to both the "Start" and "End" input fields individually, similar to how the inbuilt Filament fields work.

Available methods (replace `start` with `end` for the "End" input):
-   `startPrefix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false)`
-   `startPrefixIconColor(string | array | Closure | null $color = null)`
-   `startPrefixIcon(string | Closure | null $icon, bool | Closure $isInline = false)`
-   `startPrefixAction(Action | Closure $action, bool | Closure $isInline = false)`
-   `startPrefixActions(array $actions, bool | Closure $isInline = false)`
-   `startSuffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = true)`
-   `startSuffixIcon(string | Closure | null $icon, bool | Closure $isInline = true)`
-   `startSuffixIconColor(string | array | Closure | null $color = null)`
-   `startSuffixAction(Action | Closure $action, bool | Closure $isInline = true)`
-   `startSuffixActions(array $actions, bool | Closure $isInline = true)`
-   `inlineStartPrefix(bool | Closure $isInline = true)`
-   `inlineStartSuffix(bool | Closure $isInline = true)`

Example:
```php
use Filament\Forms\Components\Actions\Action;

DateRangePicker::make('licence_validity')
    ->startPrefix('Valid')
    ->endSuffixAction(
        Action::make('info')
            ->icon('heroicon-o-information-circle')
            ->tooltip('This range is inclusive.')
            ->action(fn () => null)
    )
```

![The "Start" input with "Valid" as a prefix label, and the "End" input with an info icon suffix action.](art/form-field-affixes.png)


## Table Filter: `DateRangeFilter`

The `DateRangeFilter` allows you to easily filter records in your Filament tables based on a date column falling within a selected range.

![A Filament table showing records filtered by a date range, with the filter indicator visible, e.g., "Order Date: Jan 15, 2024 - Jan 20, 2024".](art/table-filter-active.png)

### Basic Usage

Add the `DateRangeFilter` to your table's filters array:

```php
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;

DateRangeFilter::make('created_at'
            ->label('Created within'),
```

When activated, this filter will display the `DateRangePicker` component, allowing users to select a "start" and "end" date.

![The filter dropdown open, showing the DateRangePicker ready for input.](art/table-filter-dropdown.png)

### Filter Configuration

The `DateRangeFilter` mirrors many of the customization methods available on the `DateRangePicker` form component, passing them through to the underlying picker:

-   `displayFormat(string | Closure | null $format)`
-   `format(string | Closure | null $format)` (Note: The filter state uses `Y-m-d` by default for its internal `start` and `end` values)
-   `minDate(CarbonInterface | string | Closure | null $date)`
-   `maxDate(CarbonInterface | string | Closure | null $date)`
-   `enabledDates(array | Closure | null $dates)`: Sets specific enabled dates; all other dates will be disabled
-   `timezone(string | Closure | null $timezone)`
-   `locale(string | Closure | null $locale)`
-   `firstDayOfWeek(int | Closure $day)`
-   `startPlaceholder(string | Closure | null $placeholder)`
-   `endPlaceholder(string | Closure | null $placeholder)`
-   `autoClose(bool | Closure $condition = true)`: For filters, defaulting to `true` often provides a smoother UX, as the filter applies once the range is set and the popover closes.
-   `dualCalendar(bool | Closure $condition = true)`
-   `inline(bool | Closure $condition = true)`: Controls whether the start and end inputs are displayed horizontally (default) or vertically
-   `stacked(bool | Closure $condition = true)`: Convenience method to set vertical layout, equivalent to `->inline(false)`
-   `withTime(bool | Closure $condition = true)`: Enable time selection in the filter
-   `allDay(bool | Closure $enabled = true, bool | Closure $infer = true)`: Show all-day toggle and infer all-day state
-   `showAllDayToggle(bool | Closure $condition = true)`: Show/hide the all-day toggle when time is enabled
-   `defaultAllDay(bool | Closure $condition = true)`: Default the all-day toggle to on/off

Example:
```php
DateRangeFilter::make('processed_at')
    ->label('Processing Date')
    ->displayFormat('d M Y')
    ->minDate(now()->subYear())
```

### Query Application

The filter, by default, applies a `WHERE column >= start_date AND column <= end_date` condition to your Eloquent query. When time selection is disabled, the `start_date` is taken as the start of the selected day and `end_date` as the end of the selected day. When time selection is enabled, the exact timestamps selected in the picker are used, respecting the configured timezone.

#### `modifyQueryUsing(Closure $callback)`

For more complex filtering logic, you can provide a custom query modification callback.
```php
DateRangeFilter::make('event_span')
    ->label('Event Overlaps With')
    ->modifyQueryUsing(function (Builder $query, array $data, $state) {
        $start = $state['start'] ? Carbon::parse($state['start'])->startOfDay() : null;
        $end = $state['end'] ? Carbon::parse($state['end'])->endOfDay() : null;

        if ($start && $end) {
            return $query->where(function (Builder $query) use ($start, $end) {
                $query->where('event_starts_at', '<=', $start)
                        ->where('event_ends_at', '>=', $end);
            });
        }
        return $query;
    })
```

### Filter Indicator

The filter automatically provides an indicator when active, displaying the selected range. Example: "Created within: Jan 15, 2024 - Jan 20, 2024".
The date format used in the indicator matches the `displayFormat` of the picker.

## Localization

This package is translation-ready. It includes translations for placeholders and button labels within the picker. The following locales are currently supported:

ar, bs, ca, ckb, cs, cy, da, de, en, es, et, fa, fi, fr, hi, hu, hy, id, it, ja, ka, km, ko, ku, lt, lv, ms, my, nl, no, pl, pt_BR, pt_PT, ro, ru, sk, sl, sv, th, tr, uk, uz, vi, zh_CN, zh_TW.

To publish the translation files to your `lang/vendor/filament-date-range` directory for customization:

```bash
php artisan vendor:publish --tag="filament-date-range-translations"
```

The component will use the application's current locale by default.

## Custom Styling

The component uses a minimal set of custom CSS classes for specific structural elements (like date cell rounding for LTR/RTL) that might not be covered by Filament's default Tailwind build. These styles are automatically registered.

All color-related styling (primary colors, text, backgrounds) is designed to inherit directly from your active Filament theme, ensuring visual consistency.

If you need to deeply customize the HTML structure, you can publish the main view:

```bash
php artisan vendor:publish --tag="filament-date-range-views"
```

This will publish `date-range-picker.blade.php` to `resources/views/vendor/filament-date-range/forms/components/`. **Caution:** Modifying this directly can make updating the package more complex.

## Contributing

Found a bug or have a feature request? Please open an issue. Pull requests are also welcome!

Thank you for using Filament DateRange! I hope it enhances your Filament applications. Consider starring the repository on GitHub if you find it useful!

## License

Filament DateRange is open-sourced software licensed under the [MIT license](LICENSE).