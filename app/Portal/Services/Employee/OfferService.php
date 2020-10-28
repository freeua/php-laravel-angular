<?php

declare(strict_types=1);

namespace App\Portal\Services\Employee;

use App\Exceptions\MissingContractFieldsException;
use App\Helpers\StorageHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\ContractPrices;
use App\Documents\Models\Document;
use App\Portal\Models\Offer;
use App\Portal\Notifications\Offer\OfferSignedForAdmins;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use PDF;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Brick\Math\RoundingMode;

/**
 * Class OfferService
 *
 * @package App\Portal\Services\Employee
 */
class OfferService extends \App\Portal\Services\Base\OfferService
{

    public function getContractData(Offer $offer): array
    {
        $user = AuthHelper::user();
        $leasingCondition = $user->company
            ->activeLeasingConditionsByProductCategoryId($offer->productCategory->id)->first();
        $insuranceRates = $user->company->insuranceRates()
            ->where('product_category_id', $offer->productCategory->id)->get();
        $serviceRates = $user->company->serviceRates()
            ->where('product_category_id', $offer->productCategory->id)->get();

        return [
            'company' => $user->company,
            'leasingCondition' => $leasingCondition,
            'serviceRates' => $serviceRates,
            'insuranceRates' => $insuranceRates,
            'user' => $user,
            'offer' => $offer,
            'signatures' => [
                'company' => [
                    'date' => Carbon::now()->format('d/m/Y'),
                    'name' => $user->company->admins->first()->fullName
                ],
                'user' => [
                    'date' => Carbon::now()->format('d/m/Y'),
                    'name' => $user->fullName
                ]
            ]
        ];
    }

    public function generateContract(Offer $offer, array $data)
    {
        if ($offer->user->portal->logo) {
            $data['logo'] = \Storage::disk('public')->path($offer->user->portal->logo);
        } else {
            $data['logo'] = null;
        }
        if ($offer->user->company->logo) {
            $data['companyLogo'] = \Storage::disk('public')->path($offer->user->company->logo);
        } else {
            $data['companyLogo'] = null;
        }
        $data['footer'] = [
            'name' => $offer->user->company->name,
            'address' => $offer->user->company->address,
            'zip' => $offer->user->company->zip,
            'city' => $offer->user->company->city->name,
            'phone' => $offer->user->company->phone,
            'date' => Carbon::now()->format('m/Y')
        ];
        if (!$offer->user->hasAllContractFields() && isset($data['user'])) {
            $offer->user->fill($data['user']);
            $offer->user->save();
        } elseif (!$offer->user->hasAllContractFields() && !isset($data['user'])) {
            throw new MissingContractFieldsException($offer->user);
        }
        if (!$offer->hasAllContractFields() && $offer->user->hasAllContractFields()) {
            $this->saveEmployeeData($offer, $offer->user);
        } elseif (!$offer->hasAllContractFields() && !$offer->user->hasAllContractFields()) {
            throw new MissingContractFieldsException($offer->user);
        }
        $offer->fill([
            'contract_data' => $data,
        ]);
        $offer->saveOrFail();
        $data['contract'] = [
            'transfer_date' => Carbon::now()->format('m/Y'),
        ];
        $data['user'] = $offer->user;
        $data['offer'] = $offer;
        $prices = new ContractPrices($offer);
        $data['agreedPurchasePrice'] = Money::of($offer->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de');
        $data['totalPrice'] = Money::of($offer->productListPrice, 'EUR', null, RoundingMode::HALF_UP)->plus($offer->accessoriesPrice, RoundingMode::HALF_UP)->formatTo('de');
        $data['totalRates'] = Money::of($offer->getTotalRatesWithSubsidies(), 'EUR', null, RoundingMode::HALF_UP)->formatTo('de');
        $data['isFullySubsidied'] = Money::of($offer->getTotalRatesWithSubsidies(), 'EUR', null, RoundingMode::HALF_UP)->isZero();
        $data['period'] = $prices->getLeasingConditionApplied()->period;
        $today = Carbon::now()->format('d.m.Y');
        $data['signatures']['company'] = $today;
        $data['signatures']['user'] = $today;
        $fileName = "Ueberlassungsvertrag_{$offer->number}_{$offer->employeeName}_{$today}.pdf";
        $data['portalName'] = $offer->user->portal->name;


        $pdf = PDF::loadView('portal.offer.contract', $data)
            ->output();
        $response = response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => ' public, must-revalidate, max-age=0',
                'Pragma' => 'public',
                'X-Generator' => 'mPDF',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            ]);
        return $response;
    }

    public function uploadContractPdf(Offer $offer, UploadedFile $file): bool
    {
        $fileName = "Ueberlassungsvertrag_{$offer->number}_" . $offer->employeeName . '_' . Carbon::now()->format('Y-m-d') . '.pdf';
        $filePath = StorageHelper::store($file, $fileName, StorageHelper::PRIVATE_DISK, "/offers/{$offer->id}");

        if (!$filePath) {
            throw new FileException('Contract file could not be saved');
        }
        $document = new Document([
            'filename' => $fileName,
            'size' => $file->getSize(),
            'visible' => true,
            'extension' => 'pdf',
            'path' => $filePath,
            'manually_uploaded' => false,
            'type' => Document::SIGNED_CONTRACT,
        ]);
        $document->documentable()->associate($offer->company);
        $document->uploader()->associate($offer->user);
        $offer->document()->save($document);
        return $this->offerRepository->update($offer->id, ['contract_file' => $filePath]);
    }

    public function setPending(Offer $offer): bool
    {
        $offer->user->company->notify(new OfferSignedForAdmins($offer));

        return $this->offerRepository->update($offer->id, ['status_id' => Offer::STATUS_PENDING_APPROVAL, 'status_updated_at' => Carbon::now()]);
    }

    public function changeRates(Offer $offer, array $rates)
    {
        if (isset($rates['insuranceRate'])) {
            $offer->insuranceRate()->associate($rates['insuranceRate']['id']);
            $pricesHelper = new ContractPrices($offer);
            $offer->insuranceRateAmount = $pricesHelper->getInsuranceRate()->getAmount()->toFloat();
            $offer->insuranceRateSubsidy = $pricesHelper->getInsuranceCoverage()->getAmount()->toFloat();
        }
        if (isset($rates['serviceRate'])) {
            $offer->serviceRate()->associate($rates['serviceRate']['id']);
            $pricesHelper = new ContractPrices($offer);
            $offer->serviceRateAmount = $pricesHelper->getServiceRate()->getAmount()->toFloat();
            $offer->serviceRateSubsidy = $pricesHelper->getServiceCoverage()->getAmount()->toFloat();
        }
        $offer->save();
    }

    private function saveEmployeeData(Offer $offer, $userData)
    {
        $offer->employeeSalutation = $userData->salutation;
        $offer->employeeStreet = $userData->street;
        $offer->employeeCity = $offer->user->city->name;
        $offer->employeeNumber = $userData->employee_number;
        $offer->employeePostalCode = $userData->postal_code;
        $offer->employeePhone = $userData->phone;
        $offer->saveOrFail();
    }
}
