<?php

namespace App\Modules\Logger\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $level
 * @property string $category
 * @property string $action
 * @property string $message
 * @property array<array-key, mixed>|null $context
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $subject
 * @property-read \App\Core\Auth\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logger whereUserId($value)
 * @mixin \Eloquent
 */
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
