<?php

namespace App\Modules\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
