<?php

declare(strict_types=1);

namespace BBSLab\NovaItemsField\Tests;

use BBSLab\NovaItemsField\NovaItemsFieldServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use WithWorkbench;

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            NovaItemsFieldServiceProvider::class,
        ];
    }
}
