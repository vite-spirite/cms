<?php

namespace App\Modules\Logger\Facades;


/**
 * @method static void log(string $level, string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 * @method static void info(string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 * @method static void success(string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 * @method static void warning(string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 * @method static void error(string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 * @method static void debug(string $category, string $action, string $message, array $context = [], ?Model $subject = null)
 *
 * @see LoggerService
 */
class CmsLog extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cms.log';
    }
}
