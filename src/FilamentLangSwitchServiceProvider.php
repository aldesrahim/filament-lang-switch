<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch;

use Aldesrahim\FilamentLangSwitch\Commands\FilamentLangSwitchCommand;
use Aldesrahim\FilamentLangSwitch\Testing\TestsFilamentLangSwitch;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class FilamentLangSwitchServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-lang-switch';

    public static string $viewNamespace = 'filament-lang-switch';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(self::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('aldesrahim/filament-lang-switch');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(self::$viewNamespace);
        }

        if (filled($routes = $this->getRoutes())) {
            $package->hasRoutes($routes);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__.'/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-lang-switch/{$file->getFilename()}"),
                ], 'filament-lang-switch-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentLangSwitch);
    }

    private function getAssetPackageName(): string
    {
        return 'aldesrahim/filament-lang-switch';
    }

    /**
     * @return array<Asset>
     */
    private function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-lang-switch', __DIR__ . '/../resources/dist/components/filament-lang-switch.js'),
            Css::make('filament-lang-switch-styles', __DIR__.'/../resources/dist/filament-lang-switch.css'),
            Js::make('filament-lang-switch-scripts', __DIR__.'/../resources/dist/filament-lang-switch.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    private function getCommands(): array
    {
        return [
            FilamentLangSwitchCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    private function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getRoutes(): array
    {
        return [
            'web',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getMigrations(): array
    {
        return [
            'create_preferred_locales_table',
        ];
    }
}
