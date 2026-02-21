<?php

namespace App\Core\Module;

class ModuleHelper
{
    public static function when(string $moduleName, callable $callback): void
    {
        if (static::has($moduleName)) {
            $callback();
        }
    }

    public static function has(string $moduleName): bool
    {
        return in_array($moduleName, static::manager()->getActiveModules());
    }

    protected static function manager(): ModuleManager
    {
        return app(ModuleManager::class);
    }

    public static function unless(string $moduleName, callable $callback): void
    {
        if (!static::has($moduleName)) {
            $callback();
        }
    }
}
