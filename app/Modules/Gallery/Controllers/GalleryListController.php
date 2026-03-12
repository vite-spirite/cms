<?php

namespace App\Modules\Gallery\Controllers;

use App\Modules\Gallery\Models\Media;
use Inertia\Inertia;

class GalleryListController
{
    public function __invoke()
    {
        $medias = Media::with('uploader')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Gallery::list', [
            'medias' => $medias,
        ]);
    }
}
