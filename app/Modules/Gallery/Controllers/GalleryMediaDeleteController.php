<?php

namespace App\Modules\Gallery\Controllers;

use App\Modules\Gallery\Models\Media;

class GalleryMediaDeleteController
{
    public function __invoke(Media $media)
    {
        $disk = \Storage::disk('public');

        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        $media->delete();
        return \Redirect::back()->with('success', ['title' => 'Files Deleted', 'description' => 'Files deleted successfully.']);

    }
}
