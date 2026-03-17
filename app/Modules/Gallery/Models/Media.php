<?php

namespace App\Modules\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $label
 * @property string $path
 * @property string $url
 * @property int $uploader_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Core\Auth\Models\User $uploader
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUploaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUrl($value)
 * @mixin \Eloquent
 */
class Media extends Model
{
    public $timestamps = false;
    protected $table = 'medias';
    protected $fillable = [
        'label',
        'path',
        'url',
        'uploader_id',
        'created_at',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo('App\Core\Auth\Models\User', 'uploader_id');
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime'
        ];
    }
}
