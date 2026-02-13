<?php

namespace App\Core\Module\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
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
