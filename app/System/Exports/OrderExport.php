<?php

namespace App\System\Exports;

use App\Portal\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class OrderExport
 *
 * @package App\System\Exports
 */
class OrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /** @var Order */
    public $order;

    /**
     * OrderExport constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([$this->order]);
    }

    /**
     * @param $order
     *
     * @return array
     */
    public function map($order): array
    {
        /** @var Order $order */
        return [
            $order->number,
            $order->employeeName,
            $order->companyName,
            $order->productName,
            $order->agreedPurchasePrice,
            $order->leasingRate,
            $order->insurance,
            $order->calculatedResidualValue,
            $order->leasingPeriod,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('order.Order #'),
            __('order.First/Last Name'),
            __('order.Company Name'),
            __('order.Product Name'),
            __('order.Agreed Purchase Price'),
            __('order.Leasing Rate'),
            __('order.Insurance'),
            __('order.Calculated Residual Value'),
            __('order.Leasing Period'),
        ];
    }
}
