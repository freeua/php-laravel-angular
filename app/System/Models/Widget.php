<?php

namespace App\System\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Widget
 *
 * @package App\System\Models
 *
 * @property int        $id
 * @property int        $user_id
 * @property string     $source
 * @property string     $style
 * @property int        $position
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Collection $data
 */
class Widget extends SystemModel
{
    const STYLE_LINE = 'line';

    const STYLE_BAR = 'bar';

    const SOURCE_CONTRACTS_CREATED = 'contracts_created';

    const SOURCE_LEASING_INQUIRES = 'leasing_inquires';

    const SOURCE_NO_OF_ORDERS = 'no_of_orders';

    const SOURCE_PRODUCTS_PER_SUPPLIER = 'products_per_supplier';

    const SOURCE_COMPANY_AND_EMPLOYEES = 'company_and_employees';

    const SOURCE_NO_OF_SUPPLIERS = 'no_of_suppliers';

    const SOURCE_NO_OF_SUPPLIERS_COMPANY = 'no_of_suppliers_company';

    const USER_LIMIT = 3;

    const CHART_ITEMS_COUNT = 5;

    /**
     * @var array
     */
    protected $fillable = [
        'source',
        'style',
        'position',
    ];

    /**
     * @return array
     */
    public static function getStyles(): array
    {
        return [
            self::STYLE_LINE,
            self::STYLE_BAR
        ];
    }

    /**
     * @return array
     */
    public static function getSources(): array
    {
        return [
            self::SOURCE_CONTRACTS_CREATED,
            self::SOURCE_NO_OF_ORDERS,
            self::SOURCE_PRODUCTS_PER_SUPPLIER,
            self::SOURCE_COMPANY_AND_EMPLOYEES,
            self::SOURCE_NO_OF_SUPPLIERS,
            self::SOURCE_NO_OF_SUPPLIERS_COMPANY
        ];
    }

    /**
     * @return array
     */
    public static function getSourceTitles(): array
    {
        return [
            self::SOURCE_CONTRACTS_CREATED       => 'Verträge erstellt',
            self::SOURCE_NO_OF_ORDERS            => 'Summe der Bestellungen',
            self::SOURCE_PRODUCTS_PER_SUPPLIER   => 'Produkte je Lieferant',
            self::SOURCE_COMPANY_AND_EMPLOYEES   => 'Firma und Anzahl der Mitarbeiter',
            self::SOURCE_NO_OF_SUPPLIERS         => 'Anzahl der Lieferanten',
            self::SOURCE_NO_OF_SUPPLIERS_COMPANY => 'Anzahl der Lieferanten, die jeder Firma zugeordnet sind'
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultWidgetSources(): array
    {
        return [
            self::SOURCE_CONTRACTS_CREATED,
            self::SOURCE_NO_OF_ORDERS
        ];
    }
}
