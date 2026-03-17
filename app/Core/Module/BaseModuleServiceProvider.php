<?php

namespace App\Core\Module;

use App\Core\Navigation\Service\NavigationManager;
use App\Core\Permissions\Service\PermissionRegistry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

/**
 * Base Service Provider for all modules in CMS.
 *
 * This abstract class provides automatic loading and registration of module
 * resources including routes, migrations, commands, config, views, translations,
 * permissions, and navigation items.
 *
 * All module service providers should extend this class and set the $name property.
 *
 *
 * @example
 * ```php
 * class BlogServiceProvider extends BaseModuleServiceProvider
 * {
 *     protected string $name = 'Blog';
 *
 *     protected array $permissions = [
 *         'blog.view_posts' => [
 *             'name' => 'View blog posts',
 *             'description' => 'Ability to view blog posts',
 *         ],
 *     ];
 *
 *     public function getNavigations(): array
 *     {
 *         return [
 *             [
 *                 'label' => 'Blog',
 *                 'icon' => 'i-lucide-document',
 *                 'route' => 'blog.index',
 *             ],
 *         ];
 *     }
 * }
 * ```
 */
abstract class BaseModuleServiceProvider extends ServiceProvider
{
    /**
     * Guards to prevent double registration.
     *
     * Tracks which service providers have already been registered to prevent
     * duplicate registrations which could cause conflicts.
     *
     * @var array<int, string>
     */
    protected static array $registered = [];

    /**
     * Guards to prevent double booting.
     *
     * Tracks which service providers have already been booted to prevent
     * duplicate boot operations.
     *
     * @var array<int, string>
     */
    protected static array $booted = [];

    /**
     * The display name of the module.
     *
     * This name will be used in the admin interface, logs, and navigation.
     * Should be set by the extending class.
     *
     * @var string
     */
    protected string $name = 'CoreModule';

    /**
     * Permissions to register for this module.
     *
     * Each entry is a permission name => array pair with 'name' and 'description' keys.
     * Permissions are automatically registered with the Permission system if available.
     *
     * @var array<string, array{name: string, description: string}>
     *
     * @example
     * ```php
     * protected array $permissions = [
     *     'blog.view_posts' => [
     *         'name' => 'View blog posts',
     *         'description' => 'Ability to view all blog posts',
     *     ],
     *     'blog.create_posts' => [
     *         'name' => 'Create blog posts',
     *         'description' => 'Ability to create new blog posts',
     *     ],
     * ];
     * ```
     */
    protected array $permissions = [];

    /**
     * Register module services into the container.
     *
     * This method is called before boot() and should be used to bind
     * services, singletons, and aliases into the application container.
     * Includes a guard to prevent double registration.
     *
     * @return void
     */
    public function register(): void
    {
        if (in_array(static::class, self::$registered)) {
            return;
        }

        self::$registered[] = static::class;
    }

    /**
     * Bootstrap module services.
     *
     * This method is called after all providers are registered.
     * It automatically loads routes, migrations, commands, config, views, etc.
     * Includes a guard to prevent double booting.
     *
     * @return void
     */
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

    /**
     * Load module routes from Routes/web.php and Routes/api.php.
     *
     * Web routes are grouped with 'web' middleware.
     * API routes are grouped with 'web' and 'auth' middleware, prefixed with 'api',
     * and named with 'api.' prefix.
     *
     * @return void
     */
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

    /**
     * Get the absolute path to the module's Providers directory.
     *
     * Uses reflection to determine the directory containing the service provider class.
     *
     * @return string Absolute path to the Providers directory
     */
    protected function getModulePath(): string
    {
        $reflection = new ReflectionClass($this);

        return dirname($reflection->getFileName());
    }

    /**
     * Load module migrations from Migrations/ directory.
     *
     * All migration files in the Migrations directory are automatically
     * registered and will run when `php artisan migrate` is executed.
     *
     * @return void
     */
    protected function registerMigrations(): void
    {
        $path = $this->getModulePath() . '/../Migrations';
        if (File::isDirectory($path)) {
            $this->loadMigrationsFrom($path);
        }
    }

    /**
     * Auto-discover and register Artisan commands.
     *
     * Scans the Console/Commands directory and registers all command classes.
     * Commands are only registered when running in console mode.
     *
     * Command classes are automatically discovered based on the module's namespace.
     *
     * @return void
     */
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

    /**
     * Load task scheduling from Console/schedule.php.
     *
     * If a schedule.php file exists, it will be required and can schedule
     * tasks using the $schedule variable.
     * Only runs when in console mode and after all providers are booted.
     *
     * @return void
     *
     * @example
     * ```php
     * // Console/schedule.php
     * use Illuminate\Support\Facades\Schedule;
     *
     * Schedule::command('module:clean')->daily();
     * ```
     */
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

    /**
     * Merge module configuration files.
     *
     * All PHP files in the Config/ directory are automatically merged
     * into the application configuration using the filename as the config key.
     *
     * @return void
     *
     * @example
     * ```php
     * // Config/blog.php
     * return [
     *     'posts_per_page' => 10,
     * ];
     *
     * // Access via: config('blog.posts_per_page')
     * ```
     */
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

    /**
     * Register module translations.
     *
     * Translations can be accessed using the module namespace:
     * trans('modulename::messages.key')
     *
     * @return void
     *
     * @example
     * ```php
     * // Resources/lang/en/messages.php
     * return [
     *     'welcome' => 'Welcome to the blog',
     * ];
     *
     * // Access via: trans('blog::messages.welcome')
     * ```
     */
    protected function registerTranslations(): void
    {
        $directoryPath = $this->getModulePath() . '/../Resources/lang';
        if (!File::isDirectory($directoryPath)) {
            return;
        }

        $this->loadTranslationsFrom($directoryPath, $this->name);
    }

    /**
     * Register navigation items (if Navigation module is available).
     *
     * This method is called after all providers are booted, ensuring
     * the Navigation module is loaded if present. If the Navigation module
     * is not available, logs a warning but continues gracefully.
     *
     * @return void
     */
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

    /**
     * Get navigation items for this module.
     *
     * Override this method to provide navigation items that will be
     * added to the sidebar. Returns an empty array by default.
     *
     * @return array<int, array{label: string, icon?: string, route?: string, permission?: string, children?: array}>
     *
     * @example
     * ```php
     * public function getNavigations(): array
     * {
     *     return [
     *         [
     *             'label' => 'Blog',
     *             'icon' => 'i-lucide-document',
     *             'children' => [
     *                 ['label' => 'All Posts', 'route' => 'blog.index'],
     *                 ['label' => 'Create Post', 'route' => 'blog.create'],
     *             ],
     *         ],
     *     ];
     * }
     * ```
     */
    public function getNavigations(): array
    {
        return [];
    }

    /**
     * Register permissions (if Permission module is available).
     *
     * This method is called after all providers are booted, ensuring
     * the Permission module is loaded if present. Automatically registers
     * all permissions defined in the $permissions property.
     *
     * @return void
     */
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
