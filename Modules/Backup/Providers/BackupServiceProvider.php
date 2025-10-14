<?php

namespace Modules\Backup\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Config;
use Modules\Backup\Console\Commands\CreateDatabaseBackup;
use Modules\Backup\Console\Commands\ScheduleDatabaseBackup;
use Modules\Backup\Console\Commands\TestBackupIntelligence;
use Modules\Backup\Console\Commands\ScheduledDatabaseBackup;
use Modules\Backup\Console\Commands\ManualMysqlTest;
use Modules\Backup\Console\Commands\VerifyEnvConfig;
use Modules\Backup\Console\Commands\UbuntuTroubleshoot;
use Modules\Backup\Console\Commands\FixMySQLAuth;
use Livewire\Livewire;
use Modules\Backup\Livewire\SuperAdmin\DatabaseBackupSettings;
use Illuminate\Console\Scheduling\Schedule;

class BackupServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Backup';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'backup';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Register console commands
        $this->registerCommands();
        $this->registerCommandSchedules();

        $this->registerLivewireComponents();
    }


    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            CreateDatabaseBackup::class,
            ScheduleDatabaseBackup::class,
            TestBackupIntelligence::class,
            ScheduledDatabaseBackup::class,
            ManualMysqlTest::class,
            VerifyEnvConfig::class,
            UbuntuTroubleshoot::class,
            FixMySQLAuth::class,
        ]);
    }
    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('backup:database:schedule-check')->everyMinute();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    private function registerLivewireComponents()
    {
        Livewire::component('backup::database-backup-settings', DatabaseBackupSettings::class);
        Livewire::component('backup::super-admin.setting', DatabaseBackupSettings::class);
    }
}
