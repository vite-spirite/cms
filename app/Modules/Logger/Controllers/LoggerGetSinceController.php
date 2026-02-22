<?php

namespace App\Modules\Logger\Controllers;

use App\Modules\Logger\Models\Logger;
use Illuminate\Http\Request;

class LoggerGetSinceController
{
    public function __invoke(Request $request)
    {
        $payload = $request->validate([
            'since' => 'date'
        ]);

        $since = $payload['since'] ?? \Illuminate\Support\now()->subMinute(5)->toISOString();

        $logs = Logger::where('created_at', '>=', $since)->orderBy('created_at', 'asc')->with('subject', 'user')->get();
        $counts = Logger::selectRaw('level, count(*) as count')->where('created_at', '>=', $since)->groupBy('level')->get();

        return response()->json([
            'logs' => $logs,
            'counts' => $counts
        ]);
    }
}
