<?php

use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;

it('defaults stripTimeInAllDayDisplay to true', function () {
    $field = DateRangePicker::make('period');

    expect($field->shouldStripTimeInAllDayDisplay())->toBeTrue();
});

it('can disable stripTimeInAllDayDisplay', function () {
    $field = DateRangePicker::make('period')
        ->stripTimeInAllDayDisplay(false);

    expect($field->shouldStripTimeInAllDayDisplay())->toBeFalse();
});

it('stripTimeInAllDayDisplay is chainable and returns the field', function () {
    $field = DateRangePicker::make('period');

    expect($field->stripTimeInAllDayDisplay(false))->toBe($field);
});

it('can re-enable stripTimeInAllDayDisplay after disabling', function () {
    $field = DateRangePicker::make('period')
        ->stripTimeInAllDayDisplay(false)
        ->stripTimeInAllDayDisplay(true);

    expect($field->shouldStripTimeInAllDayDisplay())->toBeTrue();
});
