@php
    use Filament\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;

    $fieldWrapperView = $getFieldWrapperView();
    $id = $getId();
    $startId = $id . '_start';
    $endId = $id . '_end';
    $livewireKey = $getLivewireKey();
    $statePath = $getStatePath();

    $separator = $field->getSeparatorHtml();
    $isInline = $isInline();
    $isSingleField = $isSingleField();
    $presets = $getPresets();
    $showPresets = !empty($presets);
    $timeEnabled = $hasTime();
    $allDayEnabled = $isAllDayEnabled();
    $allDayInference = $shouldInferAllDay();

    $toggleOnClasses = Arr::toCssClasses([
        'fi-toggle-on',
        ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'primary'),
    ]);
    $toggleOffClasses = Arr::toCssClasses([
        'fi-toggle-off',
        ...\Filament\Support\get_component_color_classes(ToggleComponent::class, 'gray'),
    ]);

    $isRtl = in_array($getLocale(), ['ar', 'fa', 'he', 'ur']);
    $prevMonthIcon = $isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left';
    $nextMonthIcon = $isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right';

@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field" :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center">
    <div x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('date-range-picker', 'codewithkyrian/filament-date-range') }}"
        x-data="dateRangePickerFormComponent({
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            displayFormat: @js(convert_date_format($getDisplayFormat())->to('day.js')),
            stateFormat: @js(convert_date_format($getFormat())->to('day.js')),
            minDate: @js($getMinDate()),
            maxDate: @js($getMaxDate()),
            locale: @js($getLocale()),
            firstDayOfWeek: @js($getFirstDayOfWeek()),
            autoClose: @js($shouldAutoClose() && !$timeEnabled),
            dualCalendar: @js($shouldDisplayDualCalendar()),
            isReadOnly: @js($isReadOnly()),
            isDisabled: @js($isDisabled()),
            enabledDates: @js($getEnabledDates()),
            singleField: @js($isSingleField),
            timeEnabled: @js($timeEnabled),
            allDayEnabled: @js($allDayEnabled),
            allDayInference: @js($allDayInference),
            hasPresets: @js($showPresets),
            presets: @js($presets),
        })" wire:ignore x-on:click.away="if(isOpen()) cancelSelectionAndClose()"
        x-on:keydown.esc="if(isOpen()) cancelSelectionAndClose()"
        {{ $attributes->merge($getExtraAlpineAttributes(), escape: false)->class(['fi-date-range-picker', 'fi-date-range-picker-rtl' => $isRtl]) }}>

        <div x-ref="inputContainer" @class([
            'fi-date-range-picker-input-container',
            'is-inline' => $isInline,
            'is-stacked' => !$isInline,
        ])>
            {{-- Single field --}}
            @if ($isSingleField)
                <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isStartPrefixInline()" :inline-suffix="$isStartSuffixInline()" :prefix="$getStartPrefixLabel()"
                    :prefix-actions="$getStartPrefixActions()" :prefix-icon="$getStartPrefixIcon()" :prefix-icon-color="$getStartPrefixIconColor()" :suffix="$getStartSuffixLabel()" :suffix-actions="$getStartSuffixActions()"
                    :suffix-icon="$getStartSuffixIcon()" :suffix-icon-color="$getStartSuffixIconColor()" :valid="!$errors->has($statePath . '.start') && !$errors->has($statePath . '.end')"
                    class="fi-fo-date-range-picker-single-wrapper">
                    <div class="fi-date-range-picker-input-inner">
                        <input x-ref="singleInput" id="{{ $id }}" type="text" readonly x-model="rangeDisplay"
                            x-on:click="!isDisabled && !isReadOnly && openCalendar('start')"
                            placeholder="{{ $getStartPlaceholder() }}" wire:key="{{ $livewireKey }}.range-display"
                            :disabled="isDisabled || isReadOnly" class="fi-date-range-picker-input"
                            :class="{ 'is-active': isOpen() }" />
                        <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && (start || end)"
                            x-on:click.stop="clearAllDates()" class="fi-date-range-picker-clear-button"
                            title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}" x-cloak>
                            <x-filament::icon icon="heroicon-m-x-mark" class="fi-date-range-picker-clear-icon" />
                        </button>
                    </div>
                </x-filament::input.wrapper>
            @else
                {{-- Start --}}
                <div class="fi-date-range-picker-field">
                    <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isStartPrefixInline()" :inline-suffix="$isStartSuffixInline()"
                        :prefix="$getStartPrefixLabel()" :prefix-actions="$getStartPrefixActions()" :prefix-icon="$getStartPrefixIcon()" :prefix-icon-color="$getStartPrefixIconColor()" :suffix="$getStartSuffixLabel()"
                        :suffix-actions="$getStartSuffixActions()" :suffix-icon="$getStartSuffixIcon()" :suffix-icon-color="$getStartSuffixIconColor()" :valid="!$errors->has($statePath . '.start')"
                        class="fi-fo-date-range-picker-start-wrapper">
                        <div class="fi-date-range-picker-input-inner">
                            <input x-ref="startInput" id="{{ $startId }}" type="text" readonly
                                x-model="startDisplay" x-on:click="!isDisabled && !isReadOnly && openCalendar('start')"
                                placeholder="{{ $getStartPlaceholder() }}" wire:key="{{ $livewireKey }}.start-display"
                                :disabled="isDisabled || isReadOnly" class="fi-date-range-picker-input"
                                :class="{ 'is-active': isOpen() && activeEnd === 'start' }" />
                            <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && start"
                                x-on:click.stop="clearDateTarget('start')" class="fi-date-range-picker-clear-button"
                                title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}"
                                x-cloak>
                                <x-filament::icon icon="heroicon-m-x-mark" class="fi-date-range-picker-clear-icon" />
                            </button>
                        </div>
                    </x-filament::input.wrapper>
                </div>

                {{-- Separator --}}
                <div @class([
                    'fi-date-range-picker-separator',
                    'is-inline' => $isInline,
                    'is-stacked' => !$isInline,
                ])>
                    {{ $separator }}
                </div>

                {{-- End --}}
                <div class="fi-date-range-picker-field">
                    <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isEndPrefixInline()" :inline-suffix="$isEndSuffixInline()"
                        :prefix="$getEndPrefixLabel()" :prefix-actions="$getEndPrefixActions()" :prefix-icon="$getEndPrefixIcon()" :prefix-icon-color="$getEndPrefixIconColor()" :suffix="$getEndSuffixLabel()"
                        :suffix-actions="$getEndSuffixActions()" :suffix-icon="$getEndSuffixIcon()" :suffix-icon-color="$getEndSuffixIconColor()" :valid="!$errors->has($statePath . '.end')"
                        class="fi-fo-date-range-picker-end-wrapper">
                        <div class="fi-date-range-picker-input-inner">
                            <input x-ref="endInput" id="{{ $endId }}" type="text" readonly
                                x-model="endDisplay" x-on:click="!isDisabled && !isReadOnly && openCalendar('end')"
                                placeholder="{{ $getEndPlaceholder() }}" wire:key="{{ $livewireKey }}.end-display"
                                :disabled="isDisabled || isReadOnly" class="fi-date-range-picker-input"
                                :class="{ 'is-active': isOpen() && activeEnd === 'end' }" />
                            <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && end"
                                x-on:click.stop="clearDateTarget('end')" class="fi-date-range-picker-clear-button"
                                title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}"
                                x-cloak>
                                <x-filament::icon icon="heroicon-m-x-mark" class="fi-date-range-picker-clear-icon" />
                            </button>
                        </div>
                    </x-filament::input.wrapper>
                </div>
            @endif
        </div>

        {{-- Calendar Popover --}}
        <div x-ref="panel" x-cloak x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }" wire:ignore
            wire:key="{{ $livewireKey }}.panel" class="fi-date-range-picker-panel"
            :class="{
                'is-dual': dualCalendar,
                'is-single': !dualCalendar
            }">
            {{-- Header: Month/Year and Nav --}}
            <div class="fi-date-range-picker-panel-header">
                <button type="button"
                    title="{{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}"
                    x-on:click.prevent="previousMonth()" x-bind:disabled="isDisabled || isPreviousMonthDisabled()"
                    class="fi-date-range-picker-nav-button is-prev fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm">
                    <x-filament::icon :icon="$prevMonthIcon" class="fi-date-range-picker-nav-icon fi-icon-btn-icon" />
                    <span class="sr-only">
                        {{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}
                    </span>
                </button>

                {{-- Month/Year Display Area --}}
                <div class="fi-date-range-picker-months">
                    {{-- Calendar 1 Header (Month/Year) --}}
                    <div @class([
                        'fi-date-range-picker-month',
                        'is-dual' => $shouldDisplayDualCalendar(),
                        'is-single' => !$shouldDisplayDualCalendar(),
                    ])>
                        <span x-text="monthNames[currentCalendarMonth1]"></span>
                        <span x-text="currentCalendarYear1"></span>
                    </div>

                    {{-- Calendar 2 Header (Month/Year) --}}
                    <template x-if="dualCalendar">
                        <div class="fi-date-range-picker-month is-dual">
                            <span x-text="monthNames[currentCalendarMonth2]"></span>
                            <span x-text="currentCalendarYear2"></span>
                        </div>
                    </template>
                </div>

                <button type="button"
                    title="{{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}"
                    x-on:click.prevent="nextMonth()" x-bind:disabled="isDisabled || isNextMonthDisabled()"
                    class="fi-date-range-picker-nav-button is-next fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm">
                    <x-filament::icon :icon="$nextMonthIcon" class="fi-date-range-picker-nav-icon fi-icon-btn-icon" />
                    <span class="sr-only">
                        {{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}
                    </span>
                </button>
            </div>

            {{-- Presets + Dual Calendar Container --}}
            <div class="fi-date-range-picker-panel-body">
                {{-- Presets --}}
                <template x-if="hasPresets">
                    <div class="fi-date-range-picker-presets">
                        <h4 class="fi-date-range-picker-presets-heading">
                            {{ __('filament-date-range::picker.presets.heading') }}
                        </h4>
                        <div class="fi-date-range-picker-presets-list">
                            <template x-for="preset in presets" :key="preset.key">
                                <button type="button" class="fi-date-range-picker-preset"
                                    :class="{
                                        'is-active': activePreset === preset.key,
                                        'is-inactive': activePreset !== preset.key,
                                    }"
                                    x-on:click.prevent="applyPreset(preset.key)">
                                    <span x-text="preset.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Calendars --}}
                <div class="fi-date-range-picker-calendars">
                    {{-- Calendar 1 --}}
                    <div class="fi-date-range-picker-calendar">
                        {{-- Day Names --}}
                        <div class="fi-date-range-picker-calendar-header">
                            <template x-for="dayName in dayNames" :key="dayName + '_cal1'">
                                <div x-text="dayName" class="fi-date-range-picker-calendar-header-day">
                                </div>
                            </template>
                        </div>

                        {{-- Dates Grid for Calendar 1 --}}
                        <div role="grid" class="fi-date-range-picker-calendar-grid"
                            x-on:mouseleave="clearPreview()">
                            <template x-for="day in daysFromPrevMonth1" :key="'prev1_day_' + day">
                                <div x-text="day" class="fi-date-range-picker-calendar-day"></div>
                            </template>
                            <template x-for="day in daysInMonth1" :key="'cal1_day_' + day">
                                <div x-text="day"
                                    x-on:click="if (!isDayDisabled(day, currentCalendarMonth1, currentCalendarYear1)) selectDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    x-on:mouseenter="previewDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    class="fi-date-range-picker-calendar-day"
                                    :class="{
                                        'is-disabled': isDayDisabled(day, currentCalendarMonth1, currentCalendarYear1),
                                        'is-selected': isDaySelected(day, currentCalendarMonth1, currentCalendarYear1),
                                        'rounded-full': isDaySelected(day, currentCalendarMonth1,
                                            currentCalendarYear1) && !hasRange(),
                                        'ltr:rounded-l-full rtl:rounded-r-full': isStartDay(day, currentCalendarMonth1,
                                            currentCalendarYear1) && hasRange(),
                                        'ltr:rounded-r-full rtl:rounded-l-full': isEndDay(day, currentCalendarMonth1,
                                            currentCalendarYear1) && hasRange(),
                                        'is-in-range rounded-none': isInRange(day, currentCalendarMonth1,
                                            currentCalendarYear1),
                                        'is-today': isToday(day, currentCalendarMonth1, currentCalendarYear1),
                                    }">
                                </div>
                            </template>
                            <template x-for="day in daysFromNextMonth1" :key="'next1_day_' + day">
                                <div x-text="day" class="fi-date-range-picker-calendar-day is-disabled"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Calendar 2 --}}
                    <template x-if="dualCalendar">
                        <div class="fi-date-range-picker-calendar">
                            {{-- Day Names --}}
                            <div class="fi-date-range-picker-calendar-header">
                                <template x-for="dayName in dayNames" :key="dayName + '_cal2'">
                                    <div x-text="dayName" class="fi-date-range-picker-calendar-header-day"></div>
                                </template>
                            </div>

                            {{-- Dates Grid for Calendar 2 --}}
                            <div role="grid" class="fi-date-range-picker-calendar-grid"
                                x-on:mouseleave="clearPreview()">
                                <template x-for="day in daysFromPrevMonth2" :key="'prev2_day_' + day">
                                    <div x-text="day" class="fi-date-range-picker-calendar-day is-disabled"></div>
                                </template>
                                <template x-for="day in daysInMonth2" :key="'cal2_day_' + day">
                                    <div x-text="day"
                                        x-on:click="if (!isDayDisabled(day, currentCalendarMonth2, currentCalendarYear2)) selectDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        x-on:mouseenter="previewDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        class="fi-date-range-picker-calendar-day"
                                        :class="{
                                            'is-disabled': isDayDisabled(day, currentCalendarMonth2,
                                                currentCalendarYear2),
                                            'is-selected': isDaySelected(day, currentCalendarMonth2,
                                                currentCalendarYear2),
                                            'rounded-full': isDaySelected(day, currentCalendarMonth2,
                                                currentCalendarYear2) && !hasRange(),
                                            'ltr:rounded-l-full rtl:rounded-r-full': isStartDay(day,
                                                currentCalendarMonth2, currentCalendarYear2) && hasRange(),
                                            'ltr:rounded-r-full rtl:rounded-l-full': isEndDay(day,
                                                currentCalendarMonth2, currentCalendarYear2) && hasRange(),
                                            'is-in-range rounded-none': isInRange(day, currentCalendarMonth2,
                                                currentCalendarYear2),
                                            'is-today': isToday(day, currentCalendarMonth2, currentCalendarYear2),
                                        }">
                                    </div>
                                </template>
                                <template x-for="day in daysFromNextMonth2" :key="'next2_day_' + day">
                                    <div x-text="day" class="fi-date-range-picker-calendar-day"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Time Controls --}}
            <template x-if="timeEnabled">
                <div class="fi-date-range-picker-time">
                    <div x-show="allDayEnabled" class="fi-date-range-picker-all-day">
                        <span class="fi-date-range-picker-all-day-label">
                            {{ __('filament-date-range::picker.time.all_day') }}
                        </span>
                        <button type="button" role="switch" x-bind:aria-checked="allDay?.toString()"
                            x-on:click="allDay = !allDay; handleAllDayToggle()"
                            x-bind:class="allDay ? @js($toggleOnClasses) : @js($toggleOffClasses)"
                            class="fi-toggle" x-bind:disabled="isDisabled || isReadOnly">
                            <div>
                                <div aria-hidden="true"></div>
                                <div aria-hidden="true"></div>
                            </div>
                        </button>
                    </div>
                    <div class="fi-date-range-picker-time-grid">
                        <div class="fi-date-range-picker-time-field">
                            <span class="fi-date-range-time-label">
                                {{ __('filament-date-range::picker.time.start') }}
                            </span>
                            <x-filament::input.wrapper :disabled="$isDisabled()" :valid="true" class="fi-fo-text-input">
                                <input type="time" class="fi-input fi-date-range-picker-time-input"
                                    x-model="startTime" x-on:input="handleStartTimeInput($event.target.value)"
                                    :disabled="(allDayEnabled && allDay) || isDisabled || isReadOnly" />
                            </x-filament::input.wrapper>
                        </div>
                        <div class="fi-date-range-picker-time-field">
                            <span class="fi-date-range-time-label">
                                {{ __('filament-date-range::picker.time.end') }}
                            </span>
                            <x-filament::input.wrapper :disabled="$isDisabled()" :valid="true" class="fi-fo-text-input">
                                <input type="time" class="fi-input fi-date-range-picker-time-input"
                                    x-model="endTime" x-on:input="handleEndTimeInput($event.target.value)"
                                    :disabled="(allDayEnabled && allDay) || isDisabled || isReadOnly" />
                            </x-filament::input.wrapper>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Apply/Cancel buttons --}}
            <template x-if="!autoClose">
                <div class="fi-date-range-picker-footer">
                    {{-- Cancel Button --}}
                    <button type="button" x-on:click="cancelSelectionAndClose()"
                        class="fi-date-range-picker-footer-action fi-date-range-picker-cancel-button fi-link">
                        {{ __('filament-date-range::picker.buttons.cancel') }}
                    </button>

                    {{-- Apply Button --}}
                    <button type="button" x-on:click="applySelectionAndClose()"
                        class="fi-date-range-picker-footer-action fi-date-range-picker-apply-button fi-link">
                        {{ __('filament-date-range::picker.buttons.apply') }}
                    </button>
                </div>
            </template>
        </div>
    </div>
</x-dynamic-component>
