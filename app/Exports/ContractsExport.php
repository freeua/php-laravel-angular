<?php

namespace App\Exports;

use App\Portal\Models\Contract;
use Brick\Money\Money;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Contract::all();
    }

    public function headings(): array
    {
        return [
            'Nummer',
            'Mitarbeiter',
            'Lieferant',
            'Unternehmen',
            'Nutzer',
            'Produkt',
            'Auftrag',
            'Anfangsdatum',
            'Endtermin',
            "Lieferant",
            'Marke',
            'Modell',
            'Produktkategorie',
            "Produkthinweise",
            'Status',
            'Vereinbarter Kaufpreis',
            "Leasingrate",
            'Versicherung',
            "Leasingdauer",
            'Größe',
            'Farbe',
            'Hergestellt in',
            'Aktualisiert am',
            'Gelöscht um'
        ];
    }

    public function map($contract): array
    {
        return [
            $contract->number,
            $contract->employeeName,
            $contract->supplierName,
            $contract->companyName,
            $contract->employeeName,
            $contract->productBrand,
            $contract->productModel,
            $contract->order->number,
            $contract->startDate,
            $contract->endDate,
            $contract->supplierName,
            $contract->productBrand,
            $contract->productModel,
            $contract->productCategory->name,
            $contract->status->label,
            Money::of($contract->agreedPurchasePrice, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($contract->leasingRate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            Money::of($contract->insuranceRate, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de'),
            $contract->leasingPeriod . ' Monate',
            $contract->productSize,
            $contract->productColor,
            $contract->createdAt,
            $contract->updatedAt,
            $contract->deletedAt,
        ];
    }
}
