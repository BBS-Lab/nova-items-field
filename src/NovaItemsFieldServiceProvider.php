<?php

declare(strict_types=1);

namespace BBSLab\NovaItemsField;

use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaItemsFieldServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nova-items-field')
            ->hasConfigFile()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        Nova::serving(function (ServingNova $event): void {
            Nova::script('nova-items-field', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-items-field', __DIR__.'/../dist/css/field.css');
        });
    }
}
