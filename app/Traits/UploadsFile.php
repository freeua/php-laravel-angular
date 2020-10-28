<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 01/11/2018
 * Time: 13:10
 */

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait UploadsFile
{
    /**
     * @param mixed $file
     * @param null|string $folder
     *
     * @param string|null $previousFileToWipe
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function handleFile(UploadedFile $file, string $folder, string $previousFileToWipe = null): string
    {
        if ($previousFileToWipe) {
            \Storage::disk('public')->delete($previousFileToWipe);
        }
        $file = $file->store($folder, 'public');
        return $file;
    }

    public static function handlePublicJsonFile(string $base64File, string $folder, string $fileName, string $previousFileToWipe = null): string
    {
        if ($previousFileToWipe) {
            \Storage::disk('public')->delete($previousFileToWipe);
        }
        $fileContents = UploadsFile::decodeBase64($base64File);
        \Storage::disk('public')->put($folder . '/' . $fileName, $fileContents);
        return $folder . '/' . $fileName;
    }

    public static function handlePrivateJsonFile(string $base64File, string $folder, string $fileName, string $previousFileToWipe = null): string
    {
        if ($previousFileToWipe) {
            \Storage::disk('private')->delete($previousFileToWipe);
        }
        $fileContents = UploadsFile::decodeBase64($base64File);
        \Storage::disk('private')->put($folder . '/' . $fileName, $fileContents);
        return $folder . '/' . $fileName;
    }

    private static function decodeBase64(string $base64)
    {
        return base64_decode(explode(',', substr($base64, 5), 2)[1]);
    }
}
