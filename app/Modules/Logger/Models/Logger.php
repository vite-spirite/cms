<?php

namespace App\Modules\Logger\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Logger extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'cms_logger';
    protected $fillable = [
        'level',
        'category',
        'action',
        'message',
        'context',
        'subject_id',
        'subject_type',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Core\Auth\Models\User', 'user_id', 'id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime'
        ];
    }
}
