<?php

namespace App\Exports;

use App\Portal\Models\Order;
use Brick\Money\Money;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::all();
    }

    public function headings(): array
    {
        return [
            'Nummer',
            'Angebot',
            'Lieferant',
            'Mitarbeiter',
            'Firmenname',
            'Marke',
            'Modell',
            "Abgeholt von",
            "Abgeholt um",
            'Status',
            "Vereinbarter Kaufpreis",
            "Leasingrate",
            'Versicherung',
            "Leasingdauer",
            'Produktgröße',
            'Datum',
            'Angenommen bei',
            'Hergestellt in',
            'Aktualisiert am',
            'Gelöscht um'
        ];
    }

    public function map($order): array
    {
        return [
            $order->number,
            $order->offer->number,
            $order->supplierName,
            $order->employeeName,
            $order->company_name,
            $order->productBrand,
            $order->productModel,
            $order->picked_up_by,
            $order->picked_up_at,
            $order->status->label,
            Money::of($order->agreed_purchase_price, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($order->leasing_rate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($order->insurance_rate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            $order->leasing_period,
            $order->product_size,
            $order->product_color,
            $order->date,
            $order->accepted_at,
            $order->created_at,
            $order->updated_at,
            $order->deleted_at
        ];
    }
}
