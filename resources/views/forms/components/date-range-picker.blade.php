@php
    use Filament\Support\Facades\FilamentView;

    $id = $getId();
    $startId = $id . '_start';
    $endId = $id . '_end';
    $statePath = $getStatePath();

    $separator = $getSeparator();
    $isInline = $isInline();

    $isRtl = in_array($getLocale(), ['ar', 'fa', 'he', 'ur']);
    $prevMonthIcon = $isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left';
    $nextMonthIcon = $isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right';
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center">
    <div @if (FilamentView::hasSpaMode()) x-load="visible || event (ax-modal-opened)" @else x-load @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('date-range-picker', 'codewithkyrian/filament-date-range') }}"
        x-data="dateRangePickerFormComponent({
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            displayFormat: @js(convert_date_format($getDisplayFormat())->to('day.js')),
            minDate: @js($getMinDate()),
            maxDate: @js($getMaxDate()),
            locale: @js($getLocale()),
            firstDayOfWeek: @js($getFirstDayOfWeek()),
            autoClose: @js($shouldAutoClose()),
            dualCalendar: @js($shouldDisplayDualCalendar()),
            isReadOnly: @js($isReadOnly()),
            isDisabled: @js($isDisabled()),
        })" x-on:click.away="if(isOpen()) cancelSelectionAndClose()"
        x-on:keydown.esc="if(isOpen()) cancelSelectionAndClose()"
        {{ $attributes->merge($getExtraAlpineAttributes(), escape: false)->class(['fi-fo-date-range-picker']) }}>

        <div x-ref="inputContainer" @class([
            'flex',
            'items-center gap-3' => $isInline,
            'flex-col gap-2' => !$isInline,
        ])>
            {{-- Start --}}
            <div @class(['min-w-0', 'flex-1' => $isInline, 'w-full' => !$isInline])>
                <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isStartPrefixInline()" :inline-suffix="$isStartSuffixInline()" :prefix="$getStartPrefixLabel()"
                    :prefix-actions="$getStartPrefixActions()" :prefix-icon="$getStartPrefixIcon()" :prefix-icon-color="$getStartPrefixIconColor()" :suffix="$getStartSuffixLabel()" :suffix-actions="$getStartSuffixActions()"
                    :suffix-icon="$getStartSuffixIcon()" :suffix-icon-color="$getStartSuffixIconColor()" :valid="!$errors->has($statePath . '.start')"
                    class="relative fi-fo-date-range-picker-start-wrapper">
                    <div class="fi-daterange-input-container">
                        <input x-ref="startInput" id="{{ $startId }}" type="text" readonly x-model="startDisplay"
                            x-on:click="!isDisabled && !isReadOnly && openCalendar('start')"
                            placeholder="{{ $getStartPlaceholder() }}" :disabled="isDisabled || isReadOnly"
                            class="w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6"
                            :class="{ 'font-semibold': isOpen() && activeEnd === 'start' }" />
                        <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && start"
                            x-on:click.stop="clearDateTarget('start')" class="fi-daterange-clear-btn"
                            title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}" x-cloak>
                            <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </x-filament::input.wrapper>
            </div>

            {{-- Separator --}}
            <div @class([
                'inline-flex text-sm text-gray-500 dark:text-gray-400 fi-date-range-separator',
                'shrink-0' => $isInline,
                'justify-center' => !$isInline,
            ])>
                @if ($separator instanceof \Illuminate\Contracts\Support\Htmlable)
                    {!! $separator !!}
                @else
                    {{ $separator }}
                @endif
            </div>

            {{-- End --}}
            <div @class(['min-w-0', 'flex-1' => $isInline, 'w-full' => !$isInline])>
                <x-filament::input.wrapper :disabled="$isDisabled()" :inline-prefix="$isEndPrefixInline()" :inline-suffix="$isEndSuffixInline()" :prefix="$getEndPrefixLabel()"
                    :prefix-actions="$getEndPrefixActions()" :prefix-icon="$getEndPrefixIcon()" :prefix-icon-color="$getEndPrefixIconColor()" :suffix="$getEndSuffixLabel()" :suffix-actions="$getEndSuffixActions()"
                    :suffix-icon="$getEndSuffixIcon()" :suffix-icon-color="$getEndSuffixIconColor()" :valid="!$errors->has($statePath . '.end')"
                    class="fi-fo-date-range-picker-end-wrapper">
                    <div class="fi-daterange-input-container">
                        <input x-ref="endInput" id="{{ $endId }}" type="text" readonly x-model="endDisplay"
                            x-on:click="!isDisabled && !isReadOnly && openCalendar('end')"
                            placeholder="{{ $getEndPlaceholder() }}" :disabled="isDisabled || isReadOnly"
                            class="w-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6"
                            :class="{ 'font-semibold': isOpen() && activeEnd === 'end' }" />
                        <button type="button" tabindex="-1" x-show="!isReadOnly && !isDisabled && end"
                            x-on:click.stop="clearDateTarget('end')" class="fi-daterange-clear-btn"
                            title="{{ __('filament-forms::components.date_time_picker.actions.clear.label') }}" x-cloak>
                            <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </x-filament::input.wrapper>
            </div>
        </div>

        {{-- Calendar Popover --}}
        <div x-ref="panel" x-cloak x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }" wire:ignore
            wire:key="{{ $this->getId() }}.{{ $statePath }}.{{ $field::class }}.panel"
            class="absolute z-10 p-4 bg-white rounded-lg ring-1 shadow-lg ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            :style="dualCalendar ? 'min-width: 24rem;' : 'min-width: 14rem;'">

            <div class="grid gap-y-3">

                {{-- Header: Month/Year and Nav --}}
                <div class="flex relative justify-between items-center px-1 mb-2">
                    <button type="button"
                        title="{{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}"
                        x-on:click.prevent="previousMonth()" x-bind:disabled="isDisabled || isPreviousMonthDisabled()"
                        class="absolute top-0 {{ $isRtl ? 'end-0 me-auto' : 'start-0 ms-auto' }} z-10 fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm inline-flex items-center justify-center rounded-lg gap-1.5 p-2 -m-2 text-sm font-medium text-gray-700 outline-none hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-70 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5">
                        <x-filament::icon :icon="$prevMonthIcon" class="w-5 h-5 fi-icon-btn-icon" />
                        <span
                            class="sr-only">{{ __('filament-forms::components.date_time_picker.buttons.previous_month.label') }}</span>
                    </button>

                    {{-- Month/Year Display Area --}}
                    <div class="flex flex-grow justify-around items-center">
                        {{-- Calendar 1 Header (Month/Year) --}}
                        <div
                            class="flex items-center space-x-1 rtl:space-x-reverse {{ $shouldDisplayDualCalendar() ? 'w-1/2' : 'w-full' }} justify-center">
                            <span x-text="monthNames[currentCalendarMonth1]"
                                class="text-sm font-semibold text-gray-950 dark:text-white"></span>
                            <span x-text="currentCalendarYear1"
                                class="text-sm font-semibold text-gray-950 dark:text-white"></span>
                        </div>

                        {{-- Calendar 2 Header (Month/Year) --}}
                        <template x-if="dualCalendar">
                            <div class="flex justify-center items-center space-x-1 w-1/2 rtl:space-x-reverse">
                                <span x-text="monthNames[currentCalendarMonth2]"
                                    class="text-sm font-semibold text-gray-950 dark:text-white"></span>
                                <span x-text="currentCalendarYear2"
                                    class="text-sm font-semibold text-gray-950 dark:text-white"></span>
                            </div>
                        </template>
                    </div>

                    <button type="button"
                        title="{{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}"
                        x-on:click.prevent="nextMonth()" x-bind:disabled="isDisabled || isNextMonthDisabled()"
                        class="absolute top-0 {{ $isRtl ? 'start-0 ms-auto' : 'end-0 me-auto' }} z-10 fi-icon-btn fi-icon-btn-color-gray fi-icon-btn-size-sm inline-flex items-center justify-center rounded-lg gap-1.5 p-2 -m-2 text-sm font-medium text-gray-700 outline-none hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-70 dark:text-gray-200 dark:hover:bg-white/5 dark:focus:bg-white/5">
                        <x-filament::icon :icon="$nextMonthIcon" class="w-5 h-5 fi-icon-btn-icon" />
                        <span
                            class="sr-only">{{ __('filament-forms::components.date_time_picker.buttons.next_month.label') }}</span>
                    </button>
                </div>

                {{-- Dual Calendar Container --}}
                <div class="flex space-x-4 rtl:space-x-reverse">
                    {{-- Calendar 1 --}}
                    <div class="grid gap-y-1">
                        {{-- Day Names --}}
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="dayName in dayNames" :key="dayName + '_cal1'">
                                <div x-text="dayName"
                                    class="text-xs font-medium text-center text-gray-500 capitalize dark:text-gray-400">
                                </div>
                            </template>
                        </div>

                        {{-- Dates Grid for Calendar 1 --}}
                        <div role="grid" class="grid grid-cols-7 gap-0.5" x-on:mouseleave="clearPreview()">
                            <template x-for="day in daysFromPrevMonth1" :key="'prev1_day_' + day">
                                <div x-text="day" class="opacity-50 cursor-default drp-day-base"></div>
                            </template>
                            <template x-for="day in daysInMonth1" :key="'cal1_day_' + day">
                                <div x-text="day"
                                    x-on:click="if (!isDayDisabled(day, currentCalendarMonth1, currentCalendarYear1)) selectDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    x-on:mouseenter="previewDay(day, currentCalendarMonth1, currentCalendarYear1)"
                                    class="drp-day-base"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': isDayDisabled(day, currentCalendarMonth1,
                                            currentCalendarYear1),
                                        'drp-rounded-single bg-primary-600 text-white dark:bg-primary-500': isStartDay(
                                            day,
                                            currentCalendarMonth1, currentCalendarYear1) && start && (!end ||
                                            start.isSame(end, 'day')) && (!hoveredEndDate || start
                                            .isSame(hoveredEndDate, 'day')),
                                        'drp-rounded-start bg-primary-600 text-white dark:bg-primary-500': isStartDay(
                                            day,
                                            currentCalendarMonth1, currentCalendarYear1) && start && ((end && !
                                            start.isSame(end, 'day')) || (hoveredEndDate && !
                                            start.isSame(hoveredEndDate, 'day'))),
                                        'drp-rounded-end bg-primary-600 text-white dark:bg-primary-500': isEndDay(
                                            day,
                                            currentCalendarMonth1, currentCalendarYear1) && start && ((end && !
                                            start.isSame(end, 'day')) || (hoveredEndDate && !
                                            start.isSame(hoveredEndDate, 'day'))),
                                        'drp-rounded-none bg-primary-100 text-primary-700 dark:bg-primary-700/30 dark:text-primary-300': isInRange(
                                            day,
                                            currentCalendarMonth1, currentCalendarYear1),
                                        'font-semibold text-primary-600 dark:text-primary-400': isToday(day,
                                            currentCalendarMonth1, currentCalendarYear1) && !isDaySelected(day,
                                            currentCalendarMonth1, currentCalendarYear1) && !isInRange(day,
                                            currentCalendarYear1),
                                        'hover:bg-gray-100 dark:hover:bg-gray-700': !isDayDisabled(day,
                                            currentCalendarMonth1, currentCalendarYear1) && !isDaySelected(day,
                                            currentCalendarMonth1, currentCalendarYear1) && !isInRange(day,
                                            currentCalendarMonth1, currentCalendarYear1)
                                    }">
                                </div>
                            </template>
                            <template x-for="day in daysFromNextMonth1" :key="'next1_day_' + day">
                                <div x-text="day" class="opacity-50 cursor-default drp-day-base"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Calendar 2 --}}
                    <template x-if="dualCalendar">
                        <div class="grid gap-y-1">
                            {{-- Day Names --}}
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="dayName in dayNames" :key="dayName + '_cal2'">
                                    <div x-text="dayName"
                                        class="text-xs font-medium text-center text-gray-500 capitalize dark:text-gray-400">
                                    </div>
                                </template>
                            </div>

                            {{-- Dates Grid for Calendar 2 --}}
                            <div role="grid" class="grid grid-cols-7 gap-0.5" x-on:mouseleave="clearPreview()">
                                <template x-for="day in daysFromPrevMonth2" :key="'prev2_day_' + day">
                                    <div x-text="day" class="opacity-50 cursor-default drp-day-base"></div>
                                </template>
                                <template x-for="day in daysInMonth2" :key="'cal2_day_' + day">
                                    <div x-text="day"
                                        x-on:click="if (!isDayDisabled(day, currentCalendarMonth2, currentCalendarYear2)) selectDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        x-on:mouseenter="previewDay(day, currentCalendarMonth2, currentCalendarYear2)"
                                        class="drp-day-base"
                                        :class="{
                                            'opacity-50 cursor-not-allowed': isDayDisabled(day, currentCalendarMonth2,
                                                currentCalendarYear2),
                                            'drp-rounded-single bg-primary-600 text-white dark:bg-primary-500': isStartDay(
                                                day,
                                                currentCalendarMonth2, currentCalendarYear2) && start && (!end ||
                                                start.isSame(end, 'day')) && (!hoveredEndDate || start
                                                .isSame(hoveredEndDate, 'day')),
                                            'drp-rounded-start bg-primary-600 text-white dark:bg-primary-500': isStartDay(
                                                day,
                                                currentCalendarMonth2, currentCalendarYear2) && start && ((end && !
                                                start.isSame(end, 'day')) || (hoveredEndDate && !
                                                start.isSame(hoveredEndDate, 'day'))),
                                            'drp-rounded-end bg-primary-600 text-white dark:bg-primary-500': isEndDay(
                                                day,
                                                currentCalendarMonth2, currentCalendarYear2) && start && ((end && !
                                                start.isSame(end, 'day')) || (hoveredEndDate && !
                                                start.isSame(hoveredEndDate, 'day'))),
                                            'drp-rounded-none bg-primary-100 text-primary-700 dark:bg-primary-700/30 dark:text-primary-300': isInRange(
                                                day,
                                                currentCalendarMonth2, currentCalendarYear2),
                                            'font-semibold text-primary-600 dark:text-primary-400': isToday(day,
                                                currentCalendarMonth2, currentCalendarYear2) && !isDaySelected(day,
                                                currentCalendarMonth2, currentCalendarYear2) && !isInRange(day,
                                                currentCalendarYear2),
                                            'hover:bg-gray-100 dark:hover:bg-gray-700': !isDayDisabled(day,
                                                currentCalendarMonth2, currentCalendarYear2) && !isDaySelected(day,
                                                currentCalendarMonth2, currentCalendarYear2) && !isInRange(day,
                                                currentCalendarMonth2, currentCalendarYear2)
                                        }">
                                    </div>
                                </template>
                                <template x-for="day in daysFromNextMonth2" :key="'next2_day_' + day">
                                    <div x-text="day" class="opacity-50 cursor-default drp-day-base"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>


                {{-- Apply/Cancel buttons --}}
                <template x-if="!autoClose">
                    <div
                        class="flex justify-end items-center pt-4 mt-2 space-x-2 border-t border-gray-200 dark:border-gray-700 rtl:space-x-reverse">
                        {{-- Cancel Button --}}
                        <button type="button" x-on:click="cancelSelectionAndClose()"
                            class="inline-flex justify-center items-center text-xs font-medium text-gray-700 outline-none fi-link hover:underline focus:underline dark:text-gray-200 fi-btn-color-gray">
                            {{ __('filament-date-range::picker.buttons.cancel') }}
                        </button>

                        {{-- Apply Button --}}
                        <button type="button" x-on:click="applySelectionAndClose()"
                            class="inline-flex justify-center items-center text-xs font-medium outline-none fi-link text-primary-600 hover:underline focus:underline dark:text-primary-500 fi-btn-color-primary">
                            {{ __('filament-date-range::picker.buttons.apply') }}
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-dynamic-component>
