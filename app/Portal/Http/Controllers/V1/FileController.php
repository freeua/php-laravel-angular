<?php

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\StorageHelper;
use App\Http\Requests\DownloadFileRequest;
use App\Portal\Http\Controllers\Controller;
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

        if (!StorageHelper::exists($path, StorageHelper::PRIVATE_DISK)) {
            throw new NotFoundHttpException('File not found');
        }

        return StorageHelper::downloadFromDisk($path, StorageHelper::PRIVATE_DISK);
    }
}
