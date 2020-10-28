<?php

namespace App\System\Exports;

use App\Http\Requests\DefaultListRequest;
use App\System\Repositories\ContractRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Class ContractsExport
 *
 * @package App\System\Exports
 */
class ContractsExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, ShouldAutoSize
{
    /** @var ContractRepository */
    public $contractRepository;
    /** @var DefaultListRequest */
    public $request;

    /**
     * ContractsExport constructor.
     *
     * @param ContractRepository $contractRepository
     * @param DefaultListRequest $request
     */
    public function __construct(ContractRepository $contractRepository, DefaultListRequest $request)
    {
        $this->contractRepository = $contractRepository;
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->contractRepository->exportList($this->request->validated());
    }

    /**
     * @param mixed $contract
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function map($contract): array
    {
        return [
            $contract['number'],
            Date::dateTimeToExcel($contract['start_date']),
            Date::dateTimeToExcel($contract['end_date']),
            $contract['username'],
            $contract['portal_name'],
            $contract['product_name']
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD2
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('contract.Contract #'),
            __('contract.Start Date'),
            __('contract.End Date'),
            __('contract.First/Last Name'),
            __('contract.Portal Name'),
            __('contract.Product Name')
        ];
    }
}
