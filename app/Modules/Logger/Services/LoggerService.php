<?php


namespace App\Modules\Logger\Services;

use App\Modules\Logger\Models\Logger;
use Illuminate\Database\Eloquent\Model;

class LoggerService
{
    public function warning(string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        $this->log(level: 'warning', category: $category, action: $action, message: $message, context: $context, subject: $subject);
    }

    protected function log(string $level, string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        $log = Logger::make([
            'level' => $level,
            'category' => $category,
            'action' => $action,
            'message' => $message,
            'context' => $context,
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent(),
            'url' => request()->getRequestUri(),
            'created_at' => now(),
            'user_id' => auth()->id()
        ]);

        if ($subject !== null) {
            $log->subject()->associate($subject);
        }

        $log->save();
    }

    public function error(string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        $this->log(level: 'error', category: $category, action: $action, message: $message, context: $context, subject: $subject);
    }

    public function debug(string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        if (!app()->isProduction()) {
            $this->log(level: 'debug', category: $category, action: $action, message: $message, context: $context, subject: $subject);
        }
    }

    public function info(string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        $this->log(level: 'info', category: $category, action: $action, message: $message, context: $context, subject: $subject);
    }

    public function success(string $category, string $action, string $message, array $context = [], ?Model $subject = null): void
    {
        $this->log(level: 'success', category: $category, action: $action, message: $message, context: $context, subject: $subject);
    }
}
