<?php

namespace CodeWithKyrian\FilamentDateRange;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class DateRangeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-date-range';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('codewithkyrian/filament-date-range');
            });
    }

    public function packageBooted(): void
    {
        // Filament's asset registration helpers may not be present in all
        // Filament versions; only register assets when the facade exists.
        if (! class_exists(\Filament\Support\Facades\FilamentAsset::class)) {
            return;
        }

        FilamentAsset::register([
            AlpineComponent::make('date-range-picker', __DIR__ . '/../dist/components/date-range-picker.js'),
            Css::make('date-range-picker-styles', __DIR__ . '/../dist/css/date-range-picker.css'),
        ], 'codewithkyrian/filament-date-range');
    }
}
