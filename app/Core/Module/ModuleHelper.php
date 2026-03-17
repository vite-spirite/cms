<?php

namespace App\Core\Module;

/**
 * Helper class for conditional module operations.
 *
 * Provides utility methods to execute code conditionally based on whether
 * specific modules are loaded. Useful for optional integrations between modules.
 *
 *
 * @example
 * ```php
 * // Execute code only if Logger module is loaded
 * ModuleHelper::when('Logger', function () {
 *     CmsLog::info('MyModule', 'action', 'Message');
 * });
 *
 * // Execute code only if Permission module is NOT loaded
 * ModuleHelper::unless('Permission', function () {
 *     // Fallback behavior
 * });
 * ```
 */
class ModuleHelper
{
    /**
     * Execute a callback if the specified module is loaded.
     *
     * Useful for optional dependencies where you want to integrate with
     * another module only if it's available.
     *
     * @param string $moduleName The name of the module to check
     * @param callable $callback The callback to execute if module is loaded
     * @return void
     *
     * @example
     * ```php
     * ModuleHelper::when('Logger', function () {
     *     CmsLog::info('Auth', 'user.created', 'User created successfully');
     * });
     * ```
     */
    public static function when(string $moduleName, callable $callback): void
    {
        if (static::has($moduleName)) {
            $callback();
        }
    }

    /**
     * Check if a module is currently loaded.
     *
     * @param string $moduleName The name of the module to check
     * @return bool True if the module is loaded, false otherwise
     */
    public static function has(string $moduleName): bool
    {
        return in_array($moduleName, static::manager()->getActiveModules());
    }

    /**
     * Get the ModuleManager instance from the service container.
     *
     * @return ModuleManager The application's module manager
     */
    protected static function manager(): ModuleManager
    {
        return app(ModuleManager::class);
    }

    /**
     * Execute a callback if the specified module is NOT loaded.
     *
     * Useful for providing fallback behavior when an optional module
     * is not available.
     *
     * @param string $moduleName The name of the module to check
     * @param callable $callback The callback to execute if module is NOT loaded
     * @return void
     *
     * @example
     * ```php
     * ModuleHelper::unless('Permission', function () {
     *     // Provide basic authorization fallback
     * });
     * ```
     */
    public static function unless(string $moduleName, callable $callback): void
    {
        if (!static::has($moduleName)) {
            $callback();
        }
    }
}
