<?php

namespace Modules\Kitchen\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Modules\Kitchen\Console\ActivateModuleCommand;
use Modules\Kitchen\Livewire\Kitchens;
use Modules\Kitchen\Livewire\AllKitchens;
use Modules\Kitchen\Livewire\ItemKotCard;
use Modules\Kitchen\Livewire\KitchenPlaces;
use Modules\Kitchen\Livewire\AddItemToKitchen;
use Modules\Kitchen\Livewire\Forms\AddKitchen;
use Modules\Kitchen\Livewire\Forms\EditKitchen;
use Modules\Kitchen\Entities\MultipleKot;

class KitchenServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Kitchen';

    protected string $nameLower = 'kitchen';

    protected $commands = [
        ActivateModuleCommand::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Kitchen', 'Database/Migrations'));

        Livewire::component('kitchen::kitchen-places', KitchenPlaces::class);
        Livewire::component('all-kitchens', AllKitchens::class);
        Livewire::component('add-kitchen', AddKitchen::class);
        Livewire::component('edit-kitchen', EditKitchen::class);
        Livewire::component('kitchens', Kitchens::class);
        Livewire::component('add-item-to-kitchen', AddItemToKitchen::class);
        Livewire::component('item-kot-card', ItemKotCard::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands($this->commands);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/kitchen');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'kitchen');
        } else {
            $this->loadTranslationsFrom(module_path('Kitchen', 'Resources/lang'), 'kitchen');
        }
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path('Kitchen', 'Config/config.php') => config_path('kitchen.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Kitchen', 'Config/config.php'),
            'kitchen'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/kitchen');

        $sourcePath = module_path('Kitchen', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/kitchen';
        }, \Config::get('view.paths')), [$sourcePath]), 'kitchen');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
