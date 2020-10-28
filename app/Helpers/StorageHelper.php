<?php

namespace App\Helpers;

use App\Contracts\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

/**
 * Class StorageHelper
 *
 * @package App\Helpers
 * @mixin Storage
 */
abstract class StorageHelper
{
    const ROOT_DISK = 'local';

    const PUBLIC_DISK = 'public';

    const PRIVATE_DISK = 'private';

    const TENANCY_DISK = 'tenant';

    const PRIVATE_FOLDER = 'private';

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function existsRoot(string $path): bool
    {
        return Storage::disk(self::ROOT_DISK)->exists($path);
    }

    /**
     * @param string $path
     * @param string $disk
     *
     * @return bool
     */
    public static function exists(string $path, string $disk = self::PRIVATE_DISK): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * @param string $path
     * @param string $disk
     * @param string $name
     * @param array  $headers
     *
     * @return string
     */
    public static function downloadFromDisk(string $path, string $disk = self::ROOT_DISK, string $name = '', array $headers = [])
    {
        return Storage::disk($disk)->download($path, $name, $headers);
    }

    /**
     * @param string $path
     * @param string $disk
     * @param string $name
     * @param array $headers
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getFromDisk(string $path, string $disk = self::ROOT_DISK, string $name = '', array $headers = [])
    {
        return Storage::disk($disk)->get($path, $name, $headers);
    }

    /**
     * @param UploadedFile $file
     * @param string $name
     * @param string $disk
     * @param null|string $folder
     *
     * @return string|bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function store(UploadedFile $file, string $name, string $disk = self::PRIVATE_DISK, ?string $folder = null, $visibility = 'private')
    {
        $path = self::buildFilePath($file, $name, $folder);
        return Storage::disk($disk)->put($path, File::get($file), $visibility) ? $path : false;
    }


    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     *
     * @return bool
     */
    public static function deleteFromRoot($paths): bool
    {
        return Storage::disk(self::ROOT_DISK)->delete($paths);
    }

    /**
     * Delete the file at a given path and disk
     *
     * @param string|array $paths
     *
     * @param string       $disk
     *
     * @return bool
     */
    public static function delete($paths, string $disk = self::PRIVATE_DISK): bool
    {
        return Storage::disk($disk)->delete($paths);
    }

    /**
     * @param string $name
     * @param string $disk
     *
     * @return string
     */
    public static function getLocalFilePath(string $name, $disk = self::PRIVATE_DISK): string
    {
        return config(sprintf('filesystems.disks.%s.local_folder', $disk)) . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isPrivate(string $path): bool
    {
        return starts_with(ltrim($path, '/\\'), self::PRIVATE_FOLDER);
    }

    /**
     * @param User        $user
     * @param null|string $path
     *
     * @return bool
     */
    public static function userHasAccess(User $user, ?string $path = null): bool
    {
        $path = $path ?: Input::get('path');
        $path = ltrim($path, '\/');

        return $path && (!self::isPrivate($path) || starts_with($path, $user->getDefaultUserFolder()));
    }

    /**
     * @param UploadedFile $file
     * @param string       $name
     * @param null|string  $folder
     *
     * @return string
     */
    private static function buildFilePath(UploadedFile $file, string $name, ?string $folder = null): string
    {
        if (is_null($folder)) {
            $folder = Auth::user()->getDefaultUserFolder();
        }

        $path = rtrim($folder, '/\\') . DIRECTORY_SEPARATOR . ltrim($name, '/\\');

        $path .= '.' . $file->getClientOriginalExtension();

        return ltrim($path, '/\\');
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return call_user_func_array([Storage::class, $name], $arguments);
    }
}
