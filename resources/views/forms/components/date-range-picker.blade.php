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
            autoClose: @js($shouldAutoClose() && ! $timeEnabled),
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
        {{ $attributes->merge($getExtraAlpineAttributes(), escape: false)->class(['fi-date-range-picker']) }}>

        <div x-ref="inputContainer" @class([
            'flex',
            'items-center gap-3' => $isInline,
            'flex-col gap-2' => !$isInline,
        ])>
            {{-- Single field --}}
            @if ($isSingleField)
                <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isStartPrefixInline()" :inline-suffix="$isStartSuffixInline()" :prefix="$getStartPrefixLabel()"
                    :prefix-actions="$getStartPrefixActions()" :prefix-icon="$getStartPrefixIcon()" :prefix-icon-color="$getStartPrefixIconColor()" :suffix="$getStartSuffixLabel()" :suffix-actions="$getStartSuffixActions()"
                    :suffix-icon="$getStartSuffixIcon()" :suffix-icon-color="$getStartSuffixIconColor()" :valid="!$errors->has($statePath . '.start') && !$errors->has($statePath . '.end')"
                    class="relative fi-fo-date-range-picker-single-wrapper w-full">
                    <div class="relative grow">
                        <input x-ref="singleInput" id="{{ $id }}" type="text" readonly x-model="rangeDisplay"
                            x-on:click="!isDisabled && !isReadOnly && openCalendar('start')"
                            placeholder="{{ $getStartPlaceholder() }}" wire:key="{{ $livewireKey }}.range-display"
                            :disabled="isDisabled || isReadOnly"
                            class="w-full border-none bg-transparent px-3 py-1.5 text-sm leading-6 text-(--gray-950) outline-hidden transition duration-75 placeholder:text-(--gray-400) focus:ring-0 disabled:text-(--gray-500) disabled:[-webkit-text-fill-color:var(--color-gray-500)] dark:text-white dark:placeholder:text-(--gray-500) dark:disabled:text-(--gray-400) dark:disabled:[-webkit-text-fill-color:var(--color-gray-400)]"
                            :class="{ 'font-semibold': isOpen() }" />
                        <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && (start || end)"
                            x-on:click.stop="clearAllDates()"
                            class="absolute top-1/2 -translate-y-1/2 appearance-none text-(--gray-400) hover:text-(--gray-500) dark:text-(--gray-500) dark:hover:text-(--gray-400) end-2.5"
                            title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}" x-cloak>
                            <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </x-filament::input.wrapper>
            @else
                {{-- Start --}}
                <div @class(['min-w-0', 'flex-1' => $isInline, 'w-full' => !$isInline])>
                    <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isStartPrefixInline()" :inline-suffix="$isStartSuffixInline()"
                        :prefix="$getStartPrefixLabel()" :prefix-actions="$getStartPrefixActions()" :prefix-icon="$getStartPrefixIcon()" :prefix-icon-color="$getStartPrefixIconColor()" :suffix="$getStartSuffixLabel()"
                        :suffix-actions="$getStartSuffixActions()" :suffix-icon="$getStartSuffixIcon()" :suffix-icon-color="$getStartSuffixIconColor()" :valid="!$errors->has($statePath . '.start')"
                        class="relative fi-fo-date-range-picker-start-wrapper">
                        <div class="relative grow">
                            <input x-ref="startInput" id="{{ $startId }}" type="text" readonly
                                x-model="startDisplay" x-on:click="!isDisabled && !isReadOnly && openCalendar('start')"
                                placeholder="{{ $getStartPlaceholder() }}" wire:key="{{ $livewireKey }}.start-display"
                                :disabled="isDisabled || isReadOnly"
                                class="w-full border-none bg-transparent px-3 py-1.5 text-sm leading-6 text-(--gray-950) outline-hidden transition duration-75 placeholder:text-(--gray-400) focus:ring-0 disabled:text-(--gray-500) disabled:[-webkit-text-fill-color:var(--color-gray-500)] dark:text-white dark:placeholder:text-(--gray-500) dark:disabled:text-(--gray-400) dark:disabled:[-webkit-text-fill-color:var(--color-gray-400)]"
                                :class="{ 'font-semibold': isOpen() && activeEnd === 'start' }" />
                            <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && start"
                                x-on:click.stop="clearDateTarget('start')"
                                class="absolute top-1/2 -translate-y-1/2 appearance-none text-(--gray-400) hover:text-(--gray-500) dark:text-(--gray-500) dark:hover:text-(--gray-400) end-2.5"
                                title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}"
                                x-cloak>
                                <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </x-filament::input.wrapper>
                </div>

                {{-- Separator --}}
                <div @class([
                    'inline-flex text-sm text-(--gray-500) dark:text-(--gray-400) fi-date-range-separator',
                    'shrink-0' => $isInline,
                    'justify-center' => !$isInline,
                ])>
                    {{ $separator }}
                </div>

                {{-- End --}}
                <div @class(['min-w-0', 'flex-1' => $isInline, 'w-full' => !$isInline])>
                    <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isEndPrefixInline()" :inline-suffix="$isEndSuffixInline()"
                        :prefix="$getEndPrefixLabel()" :prefix-actions="$getEndPrefixActions()" :prefix-icon="$getEndPrefixIcon()" :prefix-icon-color="$getEndPrefixIconColor()" :suffix="$getEndSuffixLabel()"
                        :suffix-actions="$getEndSuffixActions()" :suffix-icon="$getEndSuffixIcon()" :suffix-icon-color="$getEndSuffixIconColor()" :valid="!$errors->has($statePath . '.end')"
                        class="fi-fo-date-range-picker-end-wrapper">
                        <div class="relative grow">
                            <input x-ref="endInput" id="{{ $endId }}" type="text" readonly
                                x-model="endDisplay" x-on:click="!isDisabled && !isReadOnly && openCalendar('end')"
                                placeholder="{{ $getEndPlaceholder() }}" wire:key="{{ $livewireKey }}.end-display"
                                :disabled="isDisabled || isReadOnly"
                                class="w-full border-none bg-transparent px-3 py-1.5 text-sm leading-6 text-(--gray-950) outline-hidden transition duration-75 placeholder:text-(--gray-400) focus:ring-0 disabled:text-(--gray-500) disabled:[-webkit-text-fill-color:var(--color-gray-500)] dark:text-white dark:placeholder:text-(--gray-500) dark:disabled:text-(--gray-400) dark:disabled:[-webkit-text-fill-color:var(--color-gray-400)]"
                                :class="{ 'font-semibold': isOpen() && activeEnd === 'end' }" />
                            <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && end"
                                x-on:click.stop="clearDateTarget('end')"
                                class="absolute top-1/2 -translate-y-1/2 appearance-none text-(--gray-400) hover:text-(--gray-500) dark:text-(--gray-500) dark:hover:text-(--gray-400) end-2.5"
                                title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}"
                                x-cloak>
                                <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </x-filament::input.wrapper>
                </div>
            @endif
        </div>

        {{-- Calendar Popover --}}
        <div x-ref="panel" x-cloak x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }" wire:ignore
            wire:key="{{ $livewireKey }}.panel"
            class="absolute z-10 p-4 bg-white dark:bg-(--gray-900) rounded-lg shadow-lg ring-1 ring-black/5 dark:ring-white/10"
            :class="{
                'min-w-96': dualCalendar,
                'min-w-56': !dualCalendar
            }">
            {{-- Header: Month/Year and Nav --}}
            <div class="flex relative justify-between items-center px-1 mb-2">
                <button type="button"
                    title="{{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}"
                    x-on:click.prevent="previousMonth()" x-bind:disabled="isDisabled || isPreviousMonthDisabled()"
                    class="absolute top-0 {{ $isRtl ? 'end-0 me-auto' : 'start-0 ms-auto' }} z-10 fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm inline-flex items-center justify-center rounded-lg gap-1.5 p-2 -m-2 text-sm font-medium text-(--gray-700) outline-none hover:bg-(--gray-50) focus:bg-(--gray-50) disabled:pointer-events-none disabled:opacity-70 dark:text-(--gray-200) dark:hover:bg-white/5 dark:focus:bg-white/5">
                    <x-filament::icon :icon="$prevMonthIcon" class="w-5 h-5 fi-icon-btn-icon" />
                    <span class="sr-only">
                        {{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}
                    </span>
                </button>

                {{-- Month/Year Display Area --}}
                <div class="flex flex-grow justify-around items-center">
                    {{-- Calendar 1 Header (Month/Year) --}}
                    <div
                        class="flex items-center space-x-1 rtl:space-x-reverse {{ $shouldDisplayDualCalendar() ? 'w-1/2' : 'w-full' }} justify-center">
                        <span x-text="monthNames[currentCalendarMonth1]"
                            class="text-sm font-semibold text-(--gray-950) dark:text-white"></span>
                        <span x-text="currentCalendarYear1"
                            class="text-sm font-semibold text-(--gray-950) dark:text-white"></span>
                    </div>

                    {{-- Calendar 2 Header (Month/Year) --}}
                    <template x-if="dualCalendar">
                        <div class="flex justify-center items-center space-x-1 w-1/2 rtl:space-x-reverse">
                            <span x-text="monthNames[currentCalendarMonth2]"
                                class="text-sm font-semibold text-(--gray-950) dark:text-white"></span>
                            <span x-text="currentCalendarYear2"
                                class="text-sm font-semibold text-(--gray-950) dark:text-white"></span>
                        </div>
                    </template>
                </div>

                <button type="button"
                    title="{{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}"
                    x-on:click.prevent="nextMonth()" x-bind:disabled="isDisabled || isNextMonthDisabled()"
                    class="absolute top-0 {{ $isRtl ? 'start-0 ms-auto' : 'end-0 me-auto' }} z-10 fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm inline-flex items-center justify-center rounded-lg gap-1.5 p-2 -m-2 text-sm font-medium text-(--gray-700) outline-none hover:bg-(--gray-50) focus:bg-(--gray-50) disabled:pointer-events-none disabled:opacity-70 dark:text-(--gray-200) dark:hover:bg-white/5 dark:focus:bg-white/5">
                    <x-filament::icon :icon="$nextMonthIcon" class="w-5 h-5 fi-icon-btn-icon" />
                    <span class="sr-only">
                        {{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}
                    </span>
                </button>
            </div>

            {{-- Presets + Dual Calendar Container --}}
            <div class="flex space-x-4 rtl:space-x-reverse">
                {{-- Presets --}}
                <template x-if="hasPresets">
                    <div class="w-40 border-r border-(--gray-200) dark:border-(--gray-700) pr-4 mr-4">
                        <h4
                            class="mb-2 text-xs font-semibold uppercase tracking-wide text-(--gray-500) dark:text-(--gray-400)">
                            {{ __('filament-date-range::picker.presets.heading') }}
                        </h4>
                        <div class="flex flex-col gap-1">
                            <template x-for="preset in presets" :key="preset.key">
                                <button type="button" class="text-left text-xs px-2 py-1 rounded-md transition-colors"
                                    :class="{
                                        'bg-(--primary-600) text-white dark:bg-(--primary-500)': activePreset === preset
                                            .key,
                                        'text-(--gray-700) hover:bg-(--gray-100) dark:text-(--gray-200) dark:hover:bg-white/5': activePreset !==
                                            preset.key,
                                    }"
                                    x-on:click.prevent="applyPreset(preset.key)">
                                    <span x-text="preset.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Calendars --}}
                <div class="flex space-x-4 rtl:space-x-reverse">
                    {{-- Calendar 1 --}}
                    <div class="grid gap-y-1">
                        {{-- Day Names --}}
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="dayName in dayNames" :key="dayName + '_cal1'">
                                <div x-text="dayName"
                                    class="text-center text-sm font-medium text-(--gray-500) dark:text-(--gray-400)">
                                </div>
                            </template>
                        </div>

                        {{-- Dates Grid for Calendar 1 --}}
                        <div role="grid" class="grid grid-cols-7 gap-y-0.5" x-on:mouseleave="clearPreview()">
                            <template x-for="day in daysFromPrevMonth1" :key="'prev1_day_' + day">
                                <div x-text="day" class="drp-calendar-day"></div>
                            </template>
                            <template x-for="day in daysInMonth1" :key="'cal1_day_' + day">
                                <div x-text="day"
                                    x-on:click="if (!isDayDisabled(day, currentCalendarMonth1, currentCalendarYear1)) selectDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    x-on:mouseenter="previewDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    class="drp-calendar-day"
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
                                <div x-text="day" class="drp-calendar-day is-disabled"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Calendar 2 --}}
                    <template x-if="dualCalendar">
                        <div class="grid gap-y-1">
                            {{-- Day Names --}}
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="dayName in dayNames" :key="dayName + '_cal2'">
                                    <div x-text="dayName" class="drp-calendar-header-day"></div>
                                </template>
                            </div>

                            {{-- Dates Grid for Calendar 2 --}}
                            <div role="grid" class="grid grid-cols-7 gap-y-0.5" x-on:mouseleave="clearPreview()">
                                <template x-for="day in daysFromPrevMonth2" :key="'prev2_day_' + day">
                                    <div x-text="day" class="drp-calendar-day is-disabled"></div>
                                </template>
                                <template x-for="day in daysInMonth2" :key="'cal2_day_' + day">
                                    <div x-text="day"
                                        x-on:click="if (!isDayDisabled(day, currentCalendarMonth2, currentCalendarYear2)) selectDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        x-on:mouseenter="previewDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        class="drp-calendar-day"
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
                                    <div x-text="day" class="drp-calendar-day"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Time Controls --}}
            <template x-if="timeEnabled">
                <div class="mt-3 pt-3 border-t border-(--gray-200) dark:border-(--gray-700) fi-date-range-time">
                    <div x-show="allDayEnabled" class="flex items-center justify-between">
                        <span class="text-xs font-medium text-(--gray-500) dark:text-(--gray-400)">
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
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <div class="grid gap-1">
                            <span class="fi-date-range-time-label">
                                {{ __('filament-date-range::picker.time.start') }}
                            </span>
                            <x-filament::input.wrapper :disabled="$isDisabled()" :valid="true" class="fi-fo-text-input">
                                <input type="time" class="fi-input w-full" x-model="startTime"
                                    x-on:input="handleStartTimeInput($event.target.value)"
                                    :disabled="(allDayEnabled && allDay) || isDisabled || isReadOnly" />
                            </x-filament::input.wrapper>
                        </div>
                        <div class="grid gap-1">
                            <span class="fi-date-range-time-label">
                                {{ __('filament-date-range::picker.time.end') }}
                            </span>
                            <x-filament::input.wrapper :disabled="$isDisabled()" :valid="true" class="fi-fo-text-input">
                                <input type="time" class="fi-input w-full" x-model="endTime"
                                    x-on:input="handleEndTimeInput($event.target.value)"
                                    :disabled="(allDayEnabled && allDay) || isDisabled || isReadOnly" />
                            </x-filament::input.wrapper>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Apply/Cancel buttons --}}
            <template x-if="!autoClose">
                <div
                    class="flex justify-end items-center pt-4 mt-2 space-x-2 border-t border-(--gray-200) dark:border-(--gray-700) rtl:space-x-reverse">
                    {{-- Cancel Button --}}
                    <button type="button" x-on:click="cancelSelectionAndClose()"
                        class="inline-flex justify-center items-center text-xs font-medium text-(--gray-700) outline-none fi-link hover:underline focus:underline dark:text-(--gray-200) fi-btn-color-gray">
                        {{ __('filament-date-range::picker.buttons.cancel') }}
                    </button>

                    {{-- Apply Button --}}
                    <button type="button" x-on:click="applySelectionAndClose()"
                        class="inline-flex justify-center items-center text-xs font-medium outline-none fi-link text-(--primary-600) hover:underline focus:underline dark:text-(--primary-500)">
                        {{ __('filament-date-range::picker.buttons.apply') }}
                    </button>
                </div>
            </template>
        </div>
    </div>
</x-dynamic-component>
