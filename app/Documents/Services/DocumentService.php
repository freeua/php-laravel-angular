<?php


namespace App\Documents\Services;

use App\Documents\Exceptions\FileTooBigException;
use App\Documents\Exceptions\WrongFileFormatException;
use App\Documents\Models\Document;
use App\Helpers\StorageHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\User;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentService
{
    const MAX_FILE_SIZE_MB = 25;

    public static function downloadDocument(Document $document): string
    {
        if (StorageHelper::disk(StorageHelper::PRIVATE_DISK)->exists($document->path)) {
            return StorageHelper::disk(StorageHelper::PRIVATE_DISK)->get($document->path);
        }

        throw new NotFoundHttpException('Document not found');
    }

    public static function deleteDocument(Document $document)
    {
        $document->delete();
    }

    public static function toggleVisibility(Document $document): Document
    {
        $document->visible = !$document->visible;
        $document->saveOrFail();
        return $document;
    }

    public static function uploadDocument(string $filename, UploadedFile $file)
    {
        $filename = $filename ?? substr($file->getClientOriginalName(), 0, -4);

        self::checkFile($file->getSize(), $file->getClientMimeType());
        $filePath = StorageHelper::store($file, 'documents/' . $filename);

        $document = new Document([
            'filename' => $filename,
            'size' => $file->getSize(),
            'visible' => true,
            'extension' => 'pdf',
            'path' => $filePath,
            'manually_uploaded' => true,
            'type' => Document::INFORMATIVE,
        ]);
        $user = auth()->user();
        $document->uploader()->associate($user);
        if ($user instanceof User && $user->isAdmin()) {
            $document->documentable()->associate(AuthHelper::user()->portal);
        }
        if ($user instanceof User && $user->isCompanyAdmin()) {
            $document->documentable()->associate(AuthHelper::user()->company);
        }
        $document->save();
        return $document;
    }

    private static function checkFile($size, $extension)
    {
        if ($size > self::MAX_FILE_SIZE_MB * pow(1024, 2)) {
            throw new FileTooBigException();
        }

        if ($extension !== 'application/pdf') {
            throw new WrongFileFormatException();
        }
    }
}
