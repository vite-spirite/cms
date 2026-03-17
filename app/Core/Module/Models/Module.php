<?php

namespace App\Core\Module\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $loaded
 * @property int $loaded_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereLoaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereLoadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @mixin \Eloquent
 */
class Module extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'loaded',
        'loaded_at',
    ];

    protected function casts()
    {
        return [
            'loaded' => 'boolean',
            'loaded_at' => 'timestamp',
        ];
    }
}
