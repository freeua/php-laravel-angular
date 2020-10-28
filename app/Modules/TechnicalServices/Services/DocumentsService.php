<?php


namespace App\Modules\TechnicalServices\Services;

use App\Documents\Models\Document;
use App\Helpers\StorageHelper;
use App\Leasings\Services\ViewModels\TechnicalServiceNoteViewModel;
use App\Modules\TechnicalServices\Models\TechnicalService;
use Illuminate\Support\Facades\Storage;

class DocumentsService
{

    public static function generateAndSaveCreditNoteTechnicalServicePdf(TechnicalService $technicalService): ?Document
    {
        $creditNotePdf = self::generateTechnicalServiceNotePdf($technicalService);
        $creditNoteName = "Gutschrift_{$technicalService->number}_{$technicalService->employeeName}_" . \Carbon\Carbon::now()->format('Y.m.d');
        $creditNotePath = "/technical-services/{$technicalService->number}/{$creditNoteName}.pdf";
        Storage::disk(StorageHelper::PRIVATE_DISK)->put($creditNotePath, $creditNotePdf);
        $technicalService->creditNoteFile = $creditNotePath;
        $technicalService->saveOrFail();

        $documentCreditNote = new Document([
            'filename' => $creditNoteName,
            'size' => Storage::disk(StorageHelper::PRIVATE_DISK)->size($creditNotePath),
            'visible' => true,
            'extension' => 'pdf',
            'path' => $creditNotePath,
            'manually_uploaded' => false,
            'type' => Document::CREDIT_NOTE
        ]);
        $documentCreditNote->documentable()->associate($technicalService->company);
        $documentCreditNote->uploader()->associate($technicalService->user);
        $technicalService->document()->save($documentCreditNote);

        // TechnicalServicesAuditsService::technicalServiceGeneratedCreditNote($technicalService, AuthHelper::user());

        return $documentCreditNote;
    }

    public static function generateTechnicalServiceNotePdf(TechnicalService $technicalService)
    {
        $pdf = \PDF::loadView('portal.order.credit-note', ['view' => new TechnicalServiceNoteViewModel($technicalService)]);
        $mpdf = $pdf->mpdf;
        $mpdf->SetImportUse();
        $pagecount = $mpdf->SetSourceFile(resource_path('pdf/mercator-template.pdf'));

        $tplIdx = $mpdf->ImportPage($pagecount);
        $mpdf->UseTemplate($tplIdx);
        return $pdf->output();
    }
}
