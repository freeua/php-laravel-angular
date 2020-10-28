<?php

namespace App\System\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Requests\DownloadFileRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FileController
 *
 * @package App\System\Http\Controllers
 */
class FileController extends Controller
{
    /**
     * Returns file by path
     *
     * @param DownloadFileRequest $request
     *
     * @return string
     */
    public function download(DownloadFileRequest $request)
    {
        $path = $request->get('path');

        if (!StorageHelper::existsRoot($path)) {
            throw new NotFoundHttpException('File not found');
        }

        return StorageHelper::downloadFromDisk($path);
    }
}
