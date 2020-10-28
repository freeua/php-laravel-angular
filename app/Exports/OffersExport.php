<?php

namespace App\Exports;

use App\Portal\Models\Offer;
use Brick\Money\Money;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OffersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Offer::all();
    }

    public function headings(): array
    {
        return [
            'Nummer',
            'Name',
            'Lieferant',
            'Produkt',
            'Unternehmen',
            'Normaler Preis',
            'Reduzierter Preis',
            "Zubehörpreis",
            "Abgelaufenes Datum",
            'Status',
            'Hergestellt in',
            'Aktualisiert am'
        ];
    }

    public function map($offer): array
    {
        return [
            $offer->number,
            $offer->employeeName,
            $offer->supplierName,
            $offer->productModel,
            $offer->user->company->name,
            Money::of($offer->productListPrice, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($offer->productDiscountedPrice, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($offer->accessories_price, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            $offer->expiry_date,
            $offer->status->label,
            $offer->created_at,
            $offer->updated_at,
        ];
    }
}
