<?php

namespace Blinq\Synth;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Blinq\Synth\Commands\SynthCommand;

class SynthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        // Inlcude the Helpers/* files
        foreach (glob(__DIR__ . '/Helpers/*.php') as $file) {
            require_once $file;
        }
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('synth')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_synth_table')
            ->hasCommand(SynthCommand::class);
    }
}
