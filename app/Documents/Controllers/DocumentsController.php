<?php


namespace App\Documents\Controllers;

use App\Documents\Models\Document;
use App\Documents\Repositories\DocumentsRepository;
use App\Documents\Requests\DeleteDocumentRequest;
use App\Documents\Requests\DocumentListRequest;
use App\Documents\Requests\DocumentListTransformer;
use App\Documents\Requests\DownloadDocumentRequest;
use App\Documents\Requests\ToggleDocumentRequest;
use App\Documents\Resources\DocumentResource;
use App\Documents\Services\DocumentService;
use Illuminate\Routing\Controller;

class DocumentsController extends Controller
{
    public function index(DocumentListRequest $request)
    {
        $documents = DocumentsRepository::listDocuments(new DocumentListTransformer($request));
        return response()->jsonPagination(DocumentResource::collection($documents));
    }

    public function upload(DocumentListRequest $request)
    {
        $document = DocumentService::uploadDocument($request->get('filename'), $request->file('file'));
        return response()->json($document);
    }

    public function download(DownloadDocumentRequest $request, Document $document)
    {
        $fileContents = DocumentService::downloadDocument($document);
        return response()->make($fileContents, 200, [
            "Content-Type" => 'application/pdf',
            "Content-Length" => $document->size,
            "Content-Disposition" => 'attachment; filename="' . $document->filename . '"'
        ]);
    }

    public function showPdf(DownloadDocumentRequest $request, Document $document)
    {
        $fileContents = DocumentService::downloadDocument($document);
        return response()->make($fileContents, 200, [
            "Content-Type" => 'application/pdf',
            "Content-Length" => $document->size,
            "Content-Disposition" => 'inline; filename="' . $document->filename . '"'
        ]);
    }

    public function delete(DeleteDocumentRequest $request, Document $document)
    {
        DocumentService::deleteDocument($document);
        return response()->json(new \stdClass());
    }

    public function toggleVisibility(ToggleDocumentRequest $request, Document $document)
    {
        $document = DocumentService::toggleVisibility($document);
        return response()->json(DocumentResource::make($document));
    }
}
