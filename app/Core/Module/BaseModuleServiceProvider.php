<?php

namespace App\Core\Module;

use App\Core\Navigation\Service\NavigationManager;
use App\Core\Permissions\Service\PermissionRegistry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    protected static array $registered = [];
    protected static array $booted = [];
    protected string $name = 'CoreModule';
    protected array $permissions = [];

    public function register(): void
    {
        if (in_array(static::class, self::$registered)) {
            return;
        }

        self::$registered[] = static::class;
    }

    public function boot(): void
    {
        if (in_array(static::class, self::$booted)) {
            return;
        }

        self::$booted[] = static::class;

        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerCommands();
        $this->registerSchedule();

        $this->registerConfig();
        $this->registerTranslations();
        $this->registerNavigations();
        $this->registerPermissions();
    }

    protected function registerRoutes(): void
    {

        $webPath = $this->getModulePath() . '/../Routes/web.php';
        $apiPath = $this->getModulePath() . '/../Routes/api.php';

        if (File::exists($webPath)) {
            \Route::middleware('web')->group($webPath);
        }

        if (File::exists($apiPath)) {
            \Route::middleware(['web', 'auth'])->prefix('api')->name('api.')->group($apiPath);
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

        $commandPath = $this->getModulePath() . '/../Console/Commands';

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

            $commandClass = $moduleNamespace . '\Console\Commands' . $relativePath;
            if (class_exists($commandClass)) {
                $commands[] = $commandClass;
            }
        }

        $this->commands($commands);
    }

    protected function registerSchedule(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $schedulePath = $this->getModulePath() . '/../Console/schedule.php';

        if (!File::exists($schedulePath)) {
            return;
        }

        $this->booted(function () use ($schedulePath) {
            require $schedulePath;
        });
    }

    protected function registerConfig(): void
    {
        $directoryPath = $this->getModulePath() . '/../Config';

        if (!File::isDirectory($directoryPath)) {
            return;
        }

        $files = File::allFiles($directoryPath);
        foreach ($files as $file) {
            $path = $file->getPath() . '/' . $file->getFilename();
            $this->mergeConfigFrom($path, $file->getFilename());
        }
    }

    protected function registerTranslations(): void
    {
        $directoryPath = $this->getModulePath() . '/../Resources/lang';
        if (!File::isDirectory($directoryPath)) {
            return;
        }

        $this->loadTranslationsFrom($directoryPath, $this->name);
    }

    protected function registerNavigations(): void
    {
        $this->booted(function () {
            if (!$this->app->bound(NavigationManager::class)) {
                Log::warning("NavigationManager is not available in {$this->name}.");
            }

            $navigationManager = $this->app->make(NavigationManager::class);
            $navigationManager->registerMany($this->getNavigations());
        });
    }

    public function getNavigations(): array
    {
        return [];
    }

    protected function registerPermissions(): void
    {
        $this->booted(function () {
            if (empty($this->permissions)) {
                return;
            }

            if (!$this->app->bound(PermissionRegistry::class)) {
                return;
            }

            $permissions = $this->app->make(PermissionRegistry::class);
            $permissions->registerMany($this->name, $this->permissions);
        });

    }
}
