<?php

namespace App\Modules\Gallery\Controllers;

use App\Modules\Gallery\Models\Media;
use Illuminate\Http\JsonResponse;

class GalleryApiListController
{
    public function __invoke(): JsonResponse
    {
        $medias = Media::orderBy('created_at', 'desc')->get();
        return \Response::json($medias);
    }
}
