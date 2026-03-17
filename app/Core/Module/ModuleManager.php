<?php

namespace App\Core\Module;

use App\Core\Module\Models\Module;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Module Manager CMS.
 *
 * Handles discovery, registration, and lifecycle management of modules.
 * Supports both Core modules (always loaded) and optional Modules (can be toggled).
 *
 */
class ModuleManager
{
    /**
     * Available modules discovered from filesystem.
     *
     * Contains module metadata from module.json files.
     *
     * @var array<string, array{name: string, display_name: string, version: string, type: string, provider: string}>
     */
    protected array $modules = [];

    /**
     * Currently active/loaded modules.
     *
     * Contains the names of modules that have been registered with the application.
     *
     * @var array<int, string>
     */
    protected array $activeModules = [];

    /**
     * Create a new Module Manager instance.
     *
     * @param Application $app The Laravel application instance
     */
    public function __construct(protected Application $app)
    {
    }

    /**
     * Get the list of currently active module names.
     *
     * @return array<int, string> Array of active module names
     */
    public function getActiveModules(): array
    {
        return $this->activeModules;
    }

    /**
     * Get all available modules discovered from the filesystem.
     *
     * @return array<string, array> Array of module metadata keyed by module name
     */
    public function getAvailableModules(): array
    {
        return $this->modules;
    }

    /**
     * Get a specific module's metadata by name.
     *
     * @param string $moduleName The name of the module to retrieve
     * @return array|null Module metadata or null if not found
     */
    public function getModule(string $moduleName): ?array
    {
        return $this->modules[$moduleName] ?? null;
    }

    /**
     * Check if a module is currently loaded.
     *
     * @param string $moduleName The name of the module to check
     * @return bool True if the module is loaded, false otherwise
     */
    public function isModuleLoaded(string $moduleName): bool
    {
        return in_array($moduleName, $this->activeModules);
    }

    /**
     * Discover all available modules from the filesystem.
     *
     * Scans both app/Core and app/Modules directories for module.json files
     * and loads their metadata.
     *
     * @return void
     */
    public function discovers(): void
    {
        $this->modules = [];

        $this->discoversModules('Core');
        $this->discoversModules('Modules');
    }

    /**
     * Discover modules in a specific application path.
     *
     * Scans the specified directory for subdirectories containing module.json files
     * and adds them to the modules registry. Logs an error if duplicate module
     * names are detected.
     *
     * @param string $appPath Path relative to app/ directory (e.g., 'Core', 'Modules')
     * @return void
     */
    protected function discoversModules(string $appPath): void
    {
        $path = app_path($appPath);
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $jsonPath = $directory . '/module.json';

            if (File::exists($jsonPath)) {
                $data = json_decode(File::get($jsonPath), true);
                if ($data && isset($data['name'])) {

                    if (Arr::has($this->modules, $data['name'])) {
                        Log::error("Module '{$data['name']}' already exists.");
                        continue;
                    }

                    $this->modules[$data['name']] = $data;
                }
            }
        }
    }

    /**
     * Load all modules of a specific type.
     *
     * Typically used to load all 'core' modules during application bootstrap.
     *
     * @param string $type The module type to load (e.g., 'core', 'module')
     * @return void
     */
    public function loadModules(string $type): void
    {
        foreach ($this->modules as $module) {
            if (isset($module['type']) && $module['type'] == $type) {
                $this->registerProvider($module);
            }
        }
    }

    /**
     * Register a module's service provider with the application.
     *
     * Checks if the module is valid, not already loaded, and has an existing
     * provider class before registration.
     *
     * @param array{name: string, provider: string} $module Module metadata
     * @return void
     */
    protected function registerProvider(array $module): void
    {
        $moduleName = $module['name'];
        $moduleProvider = $module['provider'];

        if (!$moduleName || !class_exists($moduleProvider)) {
            return;
        }

        if (in_array($moduleName, $this->activeModules)) {
            return;
        }

        $this->app->register($moduleProvider);
        $this->activeModules[] = $moduleName;
    }

    /**
     * Load a single module by name.
     *
     * Loads the module if it exists and is not already loaded.
     * Updates the database to mark the module as loaded.
     *
     * @param string $moduleName The name of the module to load
     * @return bool True if successfully loaded, false otherwise
     */
    public function loadModule(string $moduleName): bool
    {
        $module = $this->modules[$moduleName] ?? null;

        if (!$module) {
            return false;
        }

        if (in_array($moduleName, $this->activeModules)) {
            return false;
        }

        Module::updateOrCreate(['name' => $moduleName], ['name' => $moduleName, 'loaded' => true, 'loaded_at' => now()->toISOString()]);
        $this->registerProvider($module);
        return true;
    }

    /**
     * Unload a single module by name.
     *
     * Marks the module as unloaded in the database.
     * Note: Does not actually unregister the service provider as this is not
     * possible in Laravel without restarting the application.
     *
     * @param string $moduleName The name of the module to unload
     * @return bool True if successfully unloaded, false otherwise
     */
    public function unloadModule(string $moduleName): bool
    {
        $module = $this->modules[$moduleName] ?? null;

        if (!$module) {
            return false;
        }

        if (!in_array($moduleName, $this->activeModules)) {
            return false;
        }

        Module::updateOrCreate(['name' => $moduleName], ['name' => $moduleName, 'loaded' => false]);
        $this->unloadProvider($moduleName);
        return true;
    }

    /**
     * Remove a module from the active modules list.
     *
     * Note: This does not actually unregister the service provider.
     * A full application restart is required for complete unloading.
     *
     * @param string $moduleName The name of the module to unload
     * @return void
     */
    public function unloadProvider(string $moduleName): void
    {
        if (!in_array($moduleName, $this->activeModules)) {
            return;
        }

        $this->activeModules = array_filter(
            $this->activeModules,
            fn($name) => $name !== $moduleName
        );
    }

    /**
     * Load modules that are marked as loaded in the database.
     *
     * Used during application bootstrap to restore the state of optional modules.
     * Includes safety checks to avoid database access during package discovery.
     *
     * @return void
     */
    public function loadStoredModules(): void
    {
        try {
            if (!Schema::hasTable('modules')) {
                logger()->info("Stored 'modules' table does not exist.");
            }

            $moduleList = Module::orderBy('id', 'ASC')->where('loaded', true)->get();
            foreach ($moduleList as $dbModule) {
                $module = $this->modules[$dbModule->name] ?? null;
                if (!$module) {
                    Log::warning("Module '{$dbModule->name}' is stored but not found on disk.");
                    continue;
                }
                $this->registerProvider($module);
            }
        } catch (\Exception $exception) {
            logger()->info("Stored 'modules' table does not exist.", [$exception->getMessage()]);
        }
    }
}
