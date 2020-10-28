<?php

namespace App\System\Exports;

use App\Portal\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Class ContractExport
 *
 * @package App\System\Exports
 */
class ContractExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @var Contract
     */
    private $contract;

    /**
     * ContractExport constructor.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([$this->contract]);
    }

    /**
     * @param mixed $contract
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function map($contract): array
    {
        /** @var Contract $contract */
        return [
            $contract->number,
            $contract->employeeName,
            $contract->user->active_contracts,
            $contract->portal->name,
            $contract->user->company->name,
            Date::dateTimeToExcel($contract->user->created_at),
            $contract->product->supplier->name,
            $contract->productModel,
            $contract->product->category->name,
            $contract->number,
            Date::dateTimeToExcel($contract->start_date),
            Date::dateTimeToExcel($contract->end_date),
            $contract->status,
            $contract->agreedPurchasePrice,
            $contract->leasingRate,
            $contract->insurance,
            $contract->calculatedResidualValue,
            $contract->leasingPeriod,
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD2
        ];
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('contract.Contract #'),
            __('contract.First/Last Name'),
            __('contract.active_contracts'),
            __('contract.Portal Name'),
            __('contract.Company'),
            __('contract.User Active From'),
            __('contract.Product Name'),
            __('contract.Supplier'),
            __('contract.Product Category'),
            __('contract.Number'),
            __('contract.Start Date'),
            __('contract.End Date'),
            __('contract.Status'),
            __('contract.agreed_purchase_price'),
            __('contract.leasing_rate'),
            __('contract.insurance'),
            __('contract.calculated_residual_value'),
            __('contract.leasing_period'),
        ];
    }
}
