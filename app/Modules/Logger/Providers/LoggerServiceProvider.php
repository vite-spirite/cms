<?php

namespace App\Modules\Logger\Providers;

use Inertia\Inertia;

class LoggerServiceProvider extends \App\Core\Module\BaseModuleServiceProvider
{
    protected string $name = 'Logger';
    protected array $permissions = [
        'logger_view' => [
            'name' => 'View Logs',
            'description' => 'View log'
        ]
    ];

    public function register(): void
    {
        parent::register();
        $this->app->singleton(\App\Modules\Logger\Services\LoggerService::class, fn() => new \App\Modules\Logger\Services\LoggerService());
        $this->app->alias(\App\Modules\Logger\Services\LoggerService::class, 'cms.log');

        Inertia::share([
            'start_session_at' => Inertia::optional(fn() => session()->get('start_session_at') ?? tap(\Illuminate\Support\now()->toISOString(), fn($ts) => session(['start_session_at' => $ts])))
        ]);
    }
}
