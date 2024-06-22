<?php
/**
 * Created by PhpStorm.
 * User: wguedes
 * Date: 28/06/18
 * Time: 13:18
 */

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UploadHelper
{
    /**
     * Method responsible for upload image
     *
     * @param $file
     * @param $directory
     * @param null $oldImage
     * @param null $fileName
     * @param string $permission
     * @param array $dimensions
     * @return string|null
     */
    public static function upload(
        $file,
        $directory,
        $oldImage = null,
        $fileName = null,
        string $permission = 'public',
        array $dimensions = []
    ): array {
        try {
            $envDirectory = !App::environment('production') ? 'HML/' : 'PROD/';
            $path = $envDirectory . $directory;

            $allowed = explode('|', env('ALLOWED_EXTENSIONS', 'image/jpg|image/jpge|image/png|application/octet-stream'));
            $extension = $file->getClientOriginalExtension();
            $mine_type = $file->getMimeType();

            if (!in_array($mine_type, $allowed)) {
                throw new Exception(__("The type {$mine_type} is not allowed"));
            }

            if ($fileName) {
                $imageName = Str::slug($fileName, '_');
            } else {
                $imageName = md5(Str::random(16) . time());
            }

            $imageName = $imageName . '.' . $extension;

            /** delete image */
            if ($oldImage) {
                Storage::delete($oldImage);
            }

            Storage::putFileAs($path, $file, $imageName, $permission);

            return [
                'success' => true,
                'data' => [
                    'path' => "{$path}/{$imageName}",
                    'type' => strtoupper($extension)
                ]
            ];
        } catch (Exception $e) {
            Log::error("Error in upload image: {$e->getMessage()} in file {$e->getFile()}:{$e->getLine()}");

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Method responsible for delete file
     *
     * @param $path
     */
    public static function removeFile($path)
    {
        if (!App::environment('production')) {
            $path = 'HML/' . $path;
        }

        Storage::delete($path);
    }
}
