<?php

namespace App\Leasings\Services;

use App\Documents\Models\Document;
use App\Helpers\StorageHelper;
use App\Leasings\Services\ViewModels\LeasingCreditNoteViewModel;
use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Order;
use App\Portal\Notifications\Order\OrderUploadInvoiceForSysAdmin;
use App\Traits\UploadsFile;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    use UploadsFile;

    public static function generateAndSaveCreditNotePdf(Order $order): ?Document
    {
        if ($order->portal->automaticCreditNote) {
            $creditNotePdf = self::generateCreditNotePdf($order);
            $creditNoteName = "Gutschrift_{$order->number}_{$order->employeeName}_" . \Carbon\Carbon::now()->format('Y.m.d');
            $creditNotePath = "/orders/{$order->number}/{$creditNoteName}.pdf";
            Storage::disk(StorageHelper::PRIVATE_DISK)->put($creditNotePath, $creditNotePdf);
            $order->creditNoteFile = $creditNotePath;
            $order->saveOrFail();

            $documentCreditNote = new Document([
                'filename' => $creditNoteName,
                'size' => Storage::disk(StorageHelper::PRIVATE_DISK)->size($creditNotePath),
                'visible' => true,
                'extension' => 'pdf',
                'path' => $creditNotePath,
                'manually_uploaded' => false,
                'type' => Document::CREDIT_NOTE
            ]);
            $documentCreditNote->documentable()->associate($order->company);
            $documentCreditNote->uploader()->associate($order->user);
            $order->document()->save($documentCreditNote);
            return $documentCreditNote;
        }
        return null;
    }

    public static function generateAndSaveTakeoverPdf(Order $order): Document
    {
        $takeoverPdf = self::generateTakeoverPdf($order);
        $takeoverName = "Ubernahmebestatigung_{$order->number}_{$order->employeeName}_" . \Carbon\Carbon::now()->format('Y.m.d');
        $takeoverPath = "/orders/{$order->number}/{$takeoverName}.pdf";
        Storage::disk(StorageHelper::PRIVATE_DISK)->put($takeoverPath, $takeoverPdf);
        $order->takeoverFile = $takeoverPath;
        $order->saveOrFail();

        $documentTakeover = new Document([
            'filename' => $takeoverName,
            'size' => Storage::disk(StorageHelper::PRIVATE_DISK)->size($takeoverPath),
            'visible' => true,
            'extension' => 'pdf',
            'path' => $takeoverPath,
            'manually_uploaded' => false,
            'type' => Document::TAKEOVER_CERTIFICATE,
        ]);
        $documentTakeover->documentable()->associate($order->company);
        $documentTakeover->uploader()->associate($order->user);
        $order->document()->save($documentTakeover);
        return $documentTakeover;
    }

    public static function generateCreditNotePdf(Order $order)
    {
        $pdf = \PDF::loadView('portal.order.credit-note', ['view' => new LeasingCreditNoteViewModel($order)]);
        $mpdf = $pdf->mpdf;
        $mpdf->SetImportUse();
        $pagecount = $mpdf->SetSourceFile(resource_path('pdf/mercator-template.pdf'));

        $tplIdx = $mpdf->ImportPage($pagecount);
        $mpdf->UseTemplate($tplIdx);
        return $pdf->output();
    }

    public static function generateTakeoverPdf(Order $order)
    {
        $data = [];
        if ($order->user->portal->logo) {
            $data['logo'] = \Storage::disk('public')->path($order->user->portal->logo);
        } else {
            $data['logo'] = null;
        }
        if ($order->user->company->logo) {
            $data['companyLogo'] = \Storage::disk('public')->path($order->user->company->logo);
        } else {
            $data['companyLogo'] = null;
        }
        $data['offer_number'] = $order->offer->number;
        $data['order_number'] = $order->number;
        $data['company'] = [
            'name' => $order->user->company->name,
            'address' => $order->user->company->address,
            'zip' => $order->user->company->zip,
            'city' => $order->user->company->city->name,
            'phone' => $order->user->company->phone,
        ];
        $data['date'] = Carbon::now()->format('d.m.Y');
        $data['pickupCode'] = $order->pickup_code;
        $data['pickupDate'] = Carbon::now()->format('d.m.Y');

        $data['employee_name'] = $order->employeeName;
        $data['idIssueDate'] = $order->card_issue_date->format('d.m.Y');
        $data['idAuthority'] = $order->card_issue_authority;

        $data['brand'] = $order->productBrand;
        $data['model'] = $order->productModel;
        $data['frameNumber'] = $order->frame_number;

        $data['supplierName'] = $order->user->portal->name;
        return \PDF::loadView('portal.offer.certificate', $data)
            ->output();
    }


    public static function generateSingleLeasePdf(Order $order)
    {
        $data = [];
        if ($order->user->portal->logo) {
            $data['logo'] = \Storage::disk('public')->path($order->user->portal->logo);
        } else {
            $data['logo'] = null;
        }
        if ($order->user->company->logo) {
            $data['companyLogo'] = \Storage::disk('public')->path($order->user->company->logo);
        } else {
            $data['companyLogo'] = null;
        }
        $data['footer'] = [
            'name' => $order->user->company->name,
            'address' => $order->user->company->address,
            'zip' => $order->user->company->zip,
            'city' => $order->user->company->city->name,
            'phone' => $order->user->company->phone,
            'date' => $order->date->format('d.m.Y')
        ];

        $data['company'] = [
            'name' => $order->user->company->name,
            'address' => $order->user->company->address,
            'PLZOrt' => $order->user->company->zip . ' ' . $order->user->company->city->name,
        ];

        $data['employee'] = [
            'salutation' => $order->employeeSalutation,
            'name' => $order->employeeName,
            'street' => $order->employeeStreet,
            'PLZOrt' => $order->employeePostalCode . ', ' . $order->employeeCity,
            'email' => $order->employeeEmail,
            'phone' => $order->employeePhone,
            'employee_number' => $order->employeeNumber,
        ];

        $data['supplier'] = [
            'name' => $order->supplierName,
            'address' => $order->supplierStreet,
            'PLZOrt' => $order->supplierPostalCode . ' ' . $order->supplierCity,
        ];

        $data['product'] = [
            'brand' => $order->productBrand,
            'model' => $order->productModel,
            'size' => $order->productSize,
            'color' => $order->productColor,
            'type' => $order->productCategory->name,
        ];
        $data['orderNumber'] = $order->number;
        $netAgreedPurchasePrice = Money::of($order->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP)
            ->dividedBy(ContractPrices::VAT_FACTOR, RoundingMode::HALF_UP);
        $data['contract'] = array(
            'price' => $netAgreedPurchasePrice->formatTo('de'),
            'period' => $order->leasingPeriod,
            'notes' => $order->notes,
            'leasing_rate' => Money::of($order->leasingRate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            'insurance_rate' => Money::of($order->insuranceRate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            'service_rate' => Money::of($order->serviceRate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            'total_rate' => Money::of($order->leasingRate, 'EUR', null, RoundingMode::HALF_UP)
                ->plus($order->serviceRate)
                ->plus($order->insuranceRate)
                ->formatTo('de'),
            'residual' => Money::of($order->calculatedResidualValue, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
        );

        if ($order->company->invoice_type == Company::INVOICE_TYPE_GROSS) {
            $data['contract']['leasing_rate'] = Money::of($order->leasingRate, 'EUR', null, RoundingMode::HALF_UP)
                ->dividedBy(1.19, RoundingMode::HALF_UP)->formatTo('de');
            $data['contract']['insurance_rate'] = Money::of($order->insuranceRate, 'EUR', null, RoundingMode::HALF_UP)
                ->dividedBy(1.19, RoundingMode::HALF_UP)->formatTo('de');
            $data['contract']['service_rate'] = Money::of($order->serviceRate, 'EUR', null, RoundingMode::HALF_UP)
                ->dividedBy(1.19, RoundingMode::HALF_UP)->formatTo('de');
            $data['contract']['total_rate'] = Money::of($order->leasingRate, 'EUR', null, RoundingMode::HALF_UP)
                ->plus($order->serviceRate)
                ->plus($order->insuranceRate)
                ->dividedBy(1.19, RoundingMode::HALF_UP)
                ->formatTo('de');
            $data['contract']['residual'] = Money::of($order->calculatedResidualValue, 'EUR', null, RoundingMode::HALF_UP)
                ->dividedBy(1.19, RoundingMode::HALF_UP)
                ->formatTo('de');
        }

        $data['signatures']['user'] = [
            'name' => $order->user->fullName,
            'date' => $order->date->format('d.m.Y'),
        ];
        return \PDF::loadView('portal.offer.agreement', $data)
            ->output();
    }

    public static function uploadSupplierInvoice(Order $order, $fileContents)
    {
        $fileName = "Rechnung_{$order->number}.pdf";
        $order->invoice_file = UploadsFile::handlePrivateJsonFile($fileContents, "orders/{$order->number}", $fileName);
        $order->saveOrFail();

        $document = new Document([
            'filename' => $fileName,
            'size' => Storage::disk(StorageHelper::PRIVATE_DISK)->size($order->invoice_file),
            'visible' => true,
            'uploader_id' => \Auth::user()->id,
            'extension' => 'pdf',
            'path' => $order->invoice_file,
            'manually_uploaded' => true,
            'type' => Document::SUPPLIER_INVOICE,
        ]);
        $document->documentable()->associate(AuthHelper::supplier());
        $document->uploader()->associate($order->user);
        $order->document()->save($document);

        self::notifySysAdmins(new OrderUploadInvoiceForSysAdmin($order->fresh()));
    }

    public static function notifySysAdmins(Notification $notification)
    {
        $systemAdmins = \App\System\Models\User::query()->get();
        \Notification::send($systemAdmins, $notification);
    }
}
