<?php


namespace App\Modules\Gallery\Controllers;

use App\Core\Module\ModuleHelper;
use App\Modules\Gallery\Models\Media;
use App\Modules\Logger\Facades\CmsLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryUploadFilesController
{
    public function __invoke(Request $request)
    {

        try {
            $request->validate([
                'files' => ['required', 'array'],
                'files.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            ]);

            $files = $request->file('files');
            $uploadedFiles = [];

            foreach ($files as $file) {
                $name = Str::random();
                $date = Carbon::now()->timestamp;

                $path = Str::slug("{$date} {$name}") . '.' . $file->guessExtension();

                $realPath = $file->storePubliclyAs('media', $path, 'public');

                $uploadedFiles[] = [
                    'label' => $file->getClientOriginalName(),
                    'path' => $realPath,
                    'url' => asset('storage/media/' . $path),
                    'uploader_id' => $request->user()->id,
                ];
            }

            Media::insert($uploadedFiles);
            return \Redirect::back()->with('success', ['title' => 'Files Uploaded', 'description' => 'Files uploaded successfully.']);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());


            ModuleHelper::when('Logger', function () use ($e) {
                CmsLog::error('gallery', 'gallery.upload', $e->getMessage());
            });

            return \Redirect::back()->with('error', ['title' => 'Upload Failed', 'description' => 'Files uploaded unsuccessfully.']);
        }
    }
}
