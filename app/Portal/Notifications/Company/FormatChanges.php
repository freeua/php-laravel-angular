<?php

namespace App\Portal\Notifications\Company;

use App\Models\City;
use App\Models\Status;
use App\Portal\Models\Supplier;
use App\Models\Companies\Company;
use Illuminate\Support\Carbon;

class FormatChanges
{
    public $fieldMap = [
        'leasing_budget' => [
            'label' => 'Leasing Budget',
            'type' => 'Currency',
        ],
        'color' => [
            'label' => 'Hauptfarbe',
            'type' => 'Color',
        ],
        'logo' => [
            'label' => 'Logo',
            'type' => 'Logo',
        ],
        'name' => [
            'label' => 'Name',
            'type' => 'String',
        ],
        'slug' => [
            'label' => 'Slug',
            'type' => 'String',
        ],
        'vat' => [
            'label' => 'Ust-ID',
            'type' => 'String',
        ],
        'invoice_type' => [
            'label' => 'Brutto oder Netto',
            'type' => 'InvoiceType',
        ],
        'zip' => [
            'label' => 'Postleitzahl',
            'type' => 'String',
        ],
        'city_id' => [
            'label' => 'Stadt',
            'type' => 'City',
        ],
        'address' => [
            'label' => 'Strasse',
            'type' => 'String',
        ],
        'phone' => [
            'label' => 'Telefon',
            'type' => 'String',
        ],
        'max_user_contracts' => [
            'label' => 'Anzahl Vertäge pro Mitarbeiter',
            'type' => 'String',
        ],
        'max_user_amount' => [
            'label' => 'Maximaler Betrag pro Mitarbeiter',
            'type' => 'Currency',
        ],
        'min_user_amount' => [
            'label' => 'Maximaler Betrag pro Mitarbeiter',
            'type' => 'Currency',
        ],
        'override_insurance_amount' => [
            'label' => 'Anpassung Versicherung',
            'type' => 'Boolean',
        ],
        'override_maintenance_amount' => [
            'label' => 'Anpassung Servicerate',
            'type' => 'Boolean',
        ],
        'insurance_monthly_amount' => [
            'label' => 'Monatlicher Betrag',
            'type' => 'Currency',
        ],
        'insurance_covered' => [
            'label' => 'Versicherung',
            'group' => 'insurance',
            'type' => 'Boolean',
        ],
        'insurance_covered_type' => [
            'label' => 'Versicherung',
            'group' => 'insurance',
            'type' => 'TypeValue',
        ],
        'insurance_covered_amount' => [
            'label' => 'Versicherung',
            'group' => 'insurance',
            'type' => 'PercentOrCurrency',
        ],
        'maintenance_covered' => [
            'label' => 'Servicerate',
            'group' => 'maintenance',
            'type' => 'Boolean',
        ],
        'maintenance_covered_type' => [
            'label' => 'Servicerate',
            'group' => 'maintenance',
            'type' => 'TypeValue',
        ],
        'maintenance_covered_amount' => [
            'label' => 'Servicerate',
            'group' => 'maintenance',
            'type' => 'PercentOrCurrency',
        ],
        'leasing_rate' => [
            'label' => 'Leasingrate',
            'group' => 'leasing_rate',
            'type' => 'Boolean',
        ],
        'leasing_rate_type' => [
            'label' => 'Leasingrate',
            'group' => 'leasing_rate',
            'type' => 'TypeValue',
        ],
        'leasing_rate_amount' => [
            'label' => 'Leasingrate',
            'group' => 'leasing_rate',
            'type' => 'PercentOrCurrency',
        ],
        'suppliers' => [
            'label' => 'Suppliers',
            'type' => 'Suppliers',
        ],
        'subcompanies' => [
            'label' => 'Subcompanies',
            'type' => 'Subcompanies',
        ],
        'status_id' => [
            'label' => 'Status',
            'type' => 'Status',
        ],
        'is_accept_employee' => [
            'label' => 'Angebotsannahme des Mitarbeiters freischalten',
            'type' => 'boolean',
        ],
        'uses_default_subsidies' => [
            'label' => 'Uses Default Subsidies',
            'type' => 'boolean',
        ],
        's_pedelec_disable' => [
            'label' => 'Disable S-Pedelecs',
            'type' => 'boolean',
        ],
        'pecuniary_advantage' => [
            'label' => 'Zeile Geldwerter Vorteil',
            'type' => 'boolean'
        ],
        'end_contract' => [
            'label' => 'Gültigkeit Rahmenvertrag',
            'type' => 'date',
        ],
        'gross_conversion' => [
            'label' => 'Bruttolohnumwandlung / Nettolohnverzicht',
            'type' => 'GrossConversion'
        ],
        'include_service_rate' => [
            'label' => 'Service',
            'type' => 'boolean',
        ],
        'include_insurance_rate' => [
            'label' => 'Versicherung',
            'type' => 'boolean'
        ],
        'boni_number' => [
            'label' => 'Boni-Nr.',
            'type' => 'String',
        ],
        'gp_number' => [
            'label' => 'GP-Nr.',
            'type' => 'String',
        ],
    ];

    private $booleanMapLabel = [
        0 => 'Inktiv',
        1 => 'Aktiv'
    ];

    private $grossConversionMapLabel = [
        'brutto' => 'Bruttolohnumwandlung',
        'netto' => 'Nettolohnverzicht'
    ];

    private $invoiceTypeMapLabel = [
        'net' => 'Netto',
        'gross' => 'Brutto',
    ];

    private $changes = [];
    private $changedFields;
    private $oldData;

    public function __construct(array $changedFields, array $oldData)
    {
        $this->changedFields = $changedFields;
        $this->oldData = $oldData;
    }

    public function formatArrayAsTupleOfChanges()
    {
        foreach ($this->changedFields as $field => $value) {
            $fieldData = $this->fieldMap[$field];
            if (!isset($fieldData)) {
                throw new \Exception("Field $field of company model has not a mapping on 
                App/Helpers/FormatChanges. This is required for sending changes done to the company via email");
            }
            if (isset($fieldData['group'])) {
                $formatGroup = $this->formatGroup($field);
                if ($formatGroup) {
                    $this->changes[] = $formatGroup;
                }
            } else {
                $this->changes[] = $this->{'format' . $fieldData['type']}(
                    $fieldData,
                    $value,
                    $this->oldData[$field]
                );
            }
        }
        return $this->changes;
    }

    public function formatString($fieldData, $value, $oldData)
    {
        if ($value != $oldData) {
            return [$fieldData['label'], "<td>$oldData</td> <td>$value</td>"];
        } else {
            return [$fieldData['label'], "<td>$oldData</td><td>$oldData</td>"];
        }
    }

    public function formatSuppliers($fieldData, $value, $oldData)
    {
        $oldSuppliers = implode(',', Supplier::query()->whereIn('id', $oldData)->pluck('name')->toArray());
        if (count(array_diff($value, $oldData)) > 0) {
            $newSuppliers = implode(',', Supplier::query()->whereIn('id', $value)->pluck('name')->toArray());
            return [$fieldData['label'], "<td>$oldSuppliers</td> <td>$newSuppliers</td>"];
        } else {
            return [$fieldData['label'], "<td>$oldSuppliers</td><td>$oldSuppliers</td>"];
        }
    }

    public function formatSubcompanies($fieldData, $value, $oldData)
    {
        $oldSubcompanies = implode(',', Company::query()->whereIn('id', $oldData)->pluck('name')->toArray());
        if (count(array_diff($value, $oldData)) > 0 || count(array_diff($oldData, $value)) > 0) {
            $newSubcompanies = implode(',', Company::query()->whereIn('id', $value)->pluck('name')->toArray());
            return [$fieldData['label'], "<td>$oldSubcompanies</td> <td>$newSubcompanies</td>"];
        } else {
            return [$fieldData['label'], "<td>$oldSubcompanies</td><td>$newSubcompanies</td>"];
        }
    }

    public function formatColor($fieldData, $value, $oldData)
    {
        if ($value != $oldData) {
            return [$fieldData['label'], "  <td style='color: white; background-color: $oldData'>
                                                            $oldData
                                                        </td>
                                                        <td style='color: white; background-color: $value'>
                                                            $value
                                                        </td>"];
        } else {
            return [$fieldData['label'], "  <td style='color: white; background-color: $oldData'>
                                                            $oldData
                                                        </td>
                                                        <td style='color: white; background-color: $oldData'>
                                                            $oldData
                                                        </td>"];
        }
    }

    public function formatCity($fieldData, $value, $oldData)
    {
        $oldCity = City::query()->find($oldData)->name;
        if ($value != $oldData) {
            $newCity = City::query()->find($value)->name;
            return $this->formatString($fieldData, $newCity, $oldCity);
        } else {
            return $this->formatString($fieldData, $oldCity, $oldCity);
        }
    }

    public function formatStatus($fieldData, $value, $oldData)
    {
        $oldStatus = Status::query()->find($oldData)->label;
        if ($value != $oldData) {
            $newStatus = Status::query()->find($value)->label;
            return $this->formatString($fieldData, $newStatus, $oldStatus);
        } else {
            return $this->formatString($fieldData, $oldStatus, $oldStatus);
        }
    }

    public function formatLogo($fieldData, $value, $oldData)
    {
        $oldLogo = \URL::asset($oldData);
        $newLogo = \URL::asset($value);
        if ($value != $oldData) {
            return [
                $fieldData['label'],
                "<td style='float: left;'>
                    From:<br>
                    <img src='$oldLogo' alt='oldLogo' style='max-width: 200px;'/>
                   
                    </td>
                    <td style='float: right;'>
                    To:<br>
                    <img src='$newLogo'  alt='newLogo' style='max-width: 200px;'/>
                    </td>"
            ];
        } else {
            return [
                $fieldData['label'],
                "<td style='float: left;'>
                    From:<br>
                    <img src='$oldLogo'  alt='oldLogo' style='max-width: 200px;'/>
                   
                    </td>
                    <td style='float: right;'>
                    To:<br>
                    <img src='$oldLogo'  alt='newLogo' style='max-width: 200px;'/>
                    </td>"
            ];
        }
    }

    public function formatBoolean($fieldData, $value, $oldData)
    {
        $oldValue = $this->getBooleanValue($oldData);
        if ($value != $oldData) {
            $newValue = $this->getBooleanValue($value);
            return $this->formatString($fieldData, $newValue, $oldValue);
        } else {
            return $this->formatString($fieldData, $oldValue, $oldValue);
        }
    }

    public function formatDate($fieldData, $value, $oldData)
    {
        $oldValue = $this->getDateValue($oldData);
        if ($value != $oldData) {
            $newValue = $this->getDateValue($value);
            return $this->formatString($fieldData, $newValue, $oldValue);
        } else {
            return $this->formatString($fieldData, $oldValue, $oldValue);
        }
    }

    public function formatInvoiceType($fieldData, $value, $oldData)
    {
        $oldValue = $this->getInvoiceTypeValue($oldData);
        if ($value != $oldData) {
            $newValue = $this->getInvoiceTypeValue($value);
            return $this->formatString($fieldData, $newValue, $oldValue);
        } else {
            return $this->formatString($fieldData, $oldValue, $oldValue);
        }
    }

    public function formatGrossConversion($fieldData, $value, $oldData)
    {
        $oldValue = $this->getGrossConversionValue($oldData);
        if ($value != $oldData) {
            $newValue = $this->getGrossConversionValue($value);
            return $this->formatString($fieldData, $newValue, $oldValue);
        } else {
            return $this->formatString($fieldData, $oldValue, $oldValue);
        }
    }

    public function formatCurrency($fieldData, $value, $oldData)
    {
        $formatter = numfmt_create('de_DE', \NumberFormatter::CURRENCY);
        $oldNumber = numfmt_format_currency($formatter, $oldData, "EUR");
        if ($value != $oldData) {
            $newNumber = numfmt_format_currency($formatter, $value, "EUR");
            return $this->formatString($fieldData, $newNumber, $oldNumber);
        } else {
            return $this->formatString($fieldData, $oldNumber, $oldNumber);
        }
    }

    private function formatGroup($field)
    {
        $fieldData = $this->fieldMap[$field];
        $fieldAlreadyAppended = array_first($this->changes, function ($tupla) use ($fieldData) {
            return $tupla[0] === $fieldData['label'];
        });
        if (!$fieldAlreadyAppended) {
            $fieldGroup = [];
            foreach ($this->getAllFieldsOfGroup($fieldData['group']) as $fieldInGroup) {
                if (isset($this->changedFields[$fieldInGroup])) {
                    $newValue = $this->changedFields[$fieldInGroup];
                } else {
                    $newValue = $this->oldData[$fieldInGroup];
                }
                $fieldGroup[$this->fieldMap[$fieldInGroup]['type']] =
                    [
                        'fieldData' => $this->fieldMap[$fieldInGroup],
                        'newValue' => $newValue,
                        'oldValue' => $this->oldData[$fieldInGroup]
                    ];
            }
            return $this->formatString(
                $fieldData,
                !!$fieldGroup['Boolean']['newValue'] ?
                    $this->getNewPercentOrCurrencyFromFieldGroup($fieldGroup) : 'keiner',
                !!$fieldGroup['Boolean']['oldValue'] ?
                    $this->getOldPercentOrCurrencyFromFieldGroup($fieldGroup) : 'keiner'
            );
        }
        return null;
    }

    private function getAllFieldsOfGroup($group)
    {
        $fields = [];
        foreach ($this->fieldMap as $field => $fieldData) {
            if (isset($fieldData['group']) && $fieldData['group'] === $group) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    private function getOldPercentOrCurrencyFromFieldGroup($fieldGroup)
    {
        $formatter = numfmt_create('de_DE', \NumberFormatter::CURRENCY);
        if ($fieldGroup['TypeValue']['oldValue'] === 'fixed') {
            return numfmt_format_currency($formatter, $fieldGroup['PercentOrCurrency']['oldValue'], "EUR");
        } elseif ($fieldGroup['TypeValue']['oldValue'] === 'percentage') {
            return $fieldGroup['PercentOrCurrency']['oldValue'] . ' %';
        }
        return null;
    }

    private function getNewPercentOrCurrencyFromFieldGroup($fieldGroup)
    {
        $formatter = numfmt_create('de_DE', \NumberFormatter::CURRENCY);
        if ($fieldGroup['TypeValue']['newValue'] === 'fixed') {
            return numfmt_format_currency($formatter, $fieldGroup['PercentOrCurrency']['newValue'], "EUR");
        } elseif ($fieldGroup['TypeValue']['newValue'] === 'percentage') {
            return $fieldGroup['PercentOrCurrency']['newValue'] . ' %';
        }
        return null;
    }

    private function getBooleanValue($boolean)
    {
        return $this->booleanMapLabel[$boolean];
    }

    private function getDateValue($date)
    {
        return Carbon::parse($date)->format('d.m.Y');
    }

    private function getInvoiceTypeValue($invoiceType)
    {
        return $this->invoiceTypeMapLabel[$invoiceType];
    }

    private function getGrossConversionValue($grossConversion)
    {
        return $this->grossConversionMapLabel[$grossConversion];
    }
}
