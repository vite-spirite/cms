<?php

namespace App\Core\Module;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    protected string $name = 'CoreModule';

    protected array $permissions = [];

    protected array $navigations = [];

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMigrations();
    }

    protected function registerRoutes(): void
    {
        $path = $this->getModulePath().'/../Routes/web.php';

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
        $path = $this->getModulePath().'/../Migrations';
        if (File::isDirectory($path)) {
            $this->loadMigrationsFrom($path);
        }
    }
}
