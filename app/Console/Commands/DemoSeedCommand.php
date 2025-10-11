<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MenuItem;

class DemoSeedCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $this->info('----------------------------------------');
        $this->info('Starting migration and seeding...');

        try {
            DB::enableQueryLog();

            $this->info('Cleaning user-uploads folder');
            // Set the name of the directory to clean
            $directory = public_path('user-uploads');

            // Set the name of the file to exclude
            $filename_to_exclude = MenuItem::FILENAME_TO_EXCLUDE;
            // Get a list of all the files in the directory
            $files = Storage::allFiles();
            // Get all files in the directory

            // Filter out the file to exclude
            $files_to_delete = array_filter($files, function ($file) use ($filename_to_exclude) {
                return !in_array(basename($file), $filename_to_exclude);
            });

            // Delete the remaining files
            foreach ($files_to_delete as $file) {

                Storage::delete($file);
                $this->info('DELETED:' . $file);
            }

            $this->info('Cleaning Done');
        } catch (\Exception $e) {
            logger($e);
        }

        $this->call('down');

        $modules = \Nwidart\Modules\Facades\Module::allEnabled();

        foreach ($modules as $module) {
            $this->call('module:disable', ['module' => $module->getName()]);
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $modules = \Nwidart\Modules\Facades\Module::all();

        foreach ($modules as $module) {
            $this->call('module:enable', ['module' => $module->getName()]);
            $this->call('module:migrate', ['module' => $module->getName()]);
            $this->call('module:seed', ['module' => $module->getName()]);
        }



        $this->call('up');

        $executionTime = round(microtime(true) - $startTime, 2);
        $memoryUsed = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);
        $queryCount = count(DB::getQueryLog());

        $this->line("<fg=green>✓</> <fg=blue>Completed in</> <fg=yellow>{$executionTime}s</> <fg=white>|</> <fg=yellow>{$memoryUsed}MB</> <fg=white>|</> <fg=yellow>{$queryCount}</> <fg=blue>queries</>");
    }
}
