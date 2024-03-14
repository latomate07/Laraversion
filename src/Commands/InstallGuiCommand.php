<?php

namespace Laraversion\Laraversion\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class InstallGuiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraversion:install-gui';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laraversion graphical user interface.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm('Do you want to install Laraversion graphical user interface? [yes|no]', 'yes')) {
            $this->updateConfig('load_views_and_routes', true);
            $this->publishResourcesAndRoutes();

            if (!$this->isTailwindInstalled()) {
                $this->installTailwind();
                $this->configureTailwind();
            }

            $this->info('Laraversion graphical user interface installed and configured successfully.');
        } else {
            $this->info('Skipping Laraversion graphical user interface installation.');
        }

        return 0;
    }

    /**
     * Publish Laraversion resources and routes.
     *
     * @return void
     */
    private function publishResourcesAndRoutes()
    {
        $this->info('Publishing Laraversion resources and routes...');

        Artisan::call('vendor:publish', [
            '--provider' => 'Laraversion\Laraversion\LaraversionServiceProvider',
            '--tag' => 'views'
        ]);
        Artisan::call('vendor:publish', [
            '--provider' => 'Laraversion\Laraversion\LaraversionServiceProvider',
            '--tag' => 'routes'
        ]);
    }

    /**
     * Check if Tailwind CSS is installed.
     *
     * @return bool
     */
    private function isTailwindInstalled(): bool
    {
        return $this->files->exists(base_path('tailwind.config.js'));
    }

    /**
     * Install Tailwind CSS.
     *
     * @return void
     */
    private function installTailwind()
    {
        $this->info('Installing Tailwind CSS...');

        exec('npm install tailwindcss');
        exec('npx tailwindcss init');
    }

    /**
     * Configure Tailwind CSS.
     *
     * @return void
     */
    private function configureTailwind()
    {
        $this->info('Configuring Tailwind CSS...');

        $tailwindConfigPath = base_path('tailwind.config.js');
        $tailwindConfigContent = $this->files->get($tailwindConfigPath);

        $purgePathPattern = '/purge: \[\],/';
        $newPurgePathContent = "purge: ['./resources/views/**/*.blade.php', './resources/js/**/*.js'],";

        $tailwindConfigContent = preg_replace($purgePathPattern, $newPurgePathContent, $tailwindConfigContent);

        $this->files->put($tailwindConfigPath, $tailwindConfigContent);
    }

    /**
     * Update configuration file.
     *
     * @return void
     */
    private function updateConfig()
    {
        $configPath = config_path('laraversion.php');

        // Read the content of the config file
        $configContent = file_get_contents($configPath);

        // Replace the value of 'load_views_and_routes' key
        $newConfigContent = str_replace("'load_views_and_routes' => false,", "'load_views_and_routes' => true,", $configContent);

        // Write back the updated config to the file
        file_put_contents($configPath, $newConfigContent);
    }
}
