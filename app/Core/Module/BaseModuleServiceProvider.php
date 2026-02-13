<?php

namespace App\Core\Module;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    protected string $name = 'CoreModule';

    protected array $permissions = [];

    protected array $navigations = [];

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerCommands();
    }

    protected function registerRoutes(): void
    {
        $path = $this->getModulePath() . '/../Routes/web.php';

        if (File::exists($path)) {
            $this->loadRoutesFrom($path);
        }
    }

    protected function getModulePath(): string
    {
        $reflection = new \ReflectionClass($this);

        return dirname($reflection->getFileName());
    }

    protected function registerMigrations(): void
    {
        $path = $this->getModulePath() . '/../Migrations';
        if (File::isDirectory($path)) {
            $this->loadMigrationsFrom($path);
        }
    }

    protected function registerCommands(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $commandPath = $this->getModulePath() . '/../Commands';

        if (!File::isDirectory($commandPath)) {
            return;
        }

        $commands = [];
        $files = File::allFiles($commandPath);

        foreach ($files as $file) {
            $relativePath = str_replace($commandPath, '', $file->getPathname());
            $relativePath = str_replace(['/', '.php'], ['\\', ''], $relativePath);

            $reflection = new ReflectionClass($this);
            $namespace = $reflection->getNamespaceName();
            $moduleNamespace = preg_replace('/\\\\Providers$/', '', $namespace);

            $commandClass = $moduleNamespace . '\Commands' . $relativePath;

            if (class_exists($commandClass)) {
                $commands[] = $commandClass;
            }
        }

        $this->commands($commands);
    }
}
