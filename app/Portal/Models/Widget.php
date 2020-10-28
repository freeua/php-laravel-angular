<?php

namespace App\Portal\Models;

use App\Portal\Helpers\AuthHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Widget
 *
 * @package App\Portal\Models
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
class Widget extends PortalModel
{
    const STYLE_LINE = 'line';

    const STYLE_BAR = 'bar';

    const SOURCE_CONTRACTS_CREATED = 'contracts_created';

    const SOURCE_NO_OF_ORDERS = 'portal_no_of_orders';

    const SOURCE_ORDERS_PER_COMPANY = 'orders_per_company';

    const SOURCE_NO_OF_EMPLOYEES = 'no_of_employees';

    const SOURCE_OFFERS_REJECTED = 'offers_rejected';

    const SOURCE_OFFERS_ACCEPTED = 'offers_accepted';

    const SOURCE_NO_OF_ORDERS_AND_STATUS = 'no_of_orders_and_status';

    const SOURCE_NO_OF_EMPLOYEE_CONTRACTS = 'no_of_employee_contracts';

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

    protected $table = 'portal_widgets';

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
     * @param string $role
     *
     * @return array
     */
    public static function getSources(?string $role = null): array
    {
        $role = $role ?: AuthHelper::role();

        switch ($role) {
            case Role::ROLE_PORTAL_ADMIN:
                $sources = [
                    self::SOURCE_CONTRACTS_CREATED,
                    self::SOURCE_NO_OF_ORDERS,
                    self::SOURCE_ORDERS_PER_COMPANY,
                    self::SOURCE_NO_OF_EMPLOYEES
                ];
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $sources = [
                    self::SOURCE_CONTRACTS_CREATED,
                    self::SOURCE_NO_OF_ORDERS,
                    self::SOURCE_OFFERS_REJECTED,
                    self::SOURCE_OFFERS_ACCEPTED,
                    self::SOURCE_NO_OF_ORDERS_AND_STATUS,
                    self::SOURCE_NO_OF_EMPLOYEE_CONTRACTS
                ];
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $sources = [
                    self::SOURCE_ORDERS_PER_COMPANY,
                    self::SOURCE_OFFERS_REJECTED,
                    self::SOURCE_OFFERS_ACCEPTED
                ];
                break;
            default:
                $sources = [];
                break;
        }

        return $sources;
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public static function getSourceTitles(?string $role = null): array
    {
        $role = $role ?: AuthHelper::role();

        switch ($role) {
            case Role::ROLE_PORTAL_ADMIN:
                $sourceTitles = [
                    self::SOURCE_CONTRACTS_CREATED  => 'Verträge generiert',
                    self::SOURCE_NO_OF_ORDERS       => 'Bestellungen',
                    self::SOURCE_ORDERS_PER_COMPANY => 'Anzahl von Bestellungen pro Unternehmen',
                    self::SOURCE_NO_OF_EMPLOYEES    => 'Unternehmen / Mitarbeiter',
                ];
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $sourceTitles = [
                    self::SOURCE_CONTRACTS_CREATED        => 'Verträge generiert',
                    self::SOURCE_NO_OF_ORDERS             => 'Bestellungen',
                    self::SOURCE_OFFERS_REJECTED          => 'Abgelehnte Angebote',
                    self::SOURCE_OFFERS_ACCEPTED          => 'Akzeptierte Angebote',
                    self::SOURCE_NO_OF_ORDERS_AND_STATUS  => 'Anzahl der Bestellungen und Status',
                    self::SOURCE_NO_OF_EMPLOYEE_CONTRACTS => 'Anzahl der Angestelltenverträge'
                ];
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $sourceTitles = [
                    self::SOURCE_ORDERS_PER_COMPANY => 'Anzahl von Bestellungen pro Unternehmen',
                    self::SOURCE_OFFERS_REJECTED    => 'Abgelehnte Angebote',
                    self::SOURCE_OFFERS_ACCEPTED    => 'Akzeptierte Angebote',
                ];
                break;
            default:
                $sourceTitles = [];
                break;
        }

        return $sourceTitles;
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public static function getDefaultWidgetSources(?string $role = null): array
    {
        $role = $role ?: AuthHelper::role();

        switch ($role) {
            case Role::ROLE_PORTAL_ADMIN:
                $sources = [
                    self::SOURCE_NO_OF_EMPLOYEES,
                    self::SOURCE_CONTRACTS_CREATED,
                    self::SOURCE_NO_OF_ORDERS
                ];
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $sources = [
                    self::SOURCE_CONTRACTS_CREATED,
                    self::SOURCE_NO_OF_ORDERS,
                    self::SOURCE_OFFERS_REJECTED
                ];
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $sources = [
                    self::SOURCE_ORDERS_PER_COMPANY,
                    self::SOURCE_OFFERS_ACCEPTED,
                    self::SOURCE_OFFERS_REJECTED
                ];
                break;
            default:
                $sources = [];
                break;
        }

        return $sources;
    }

    /**
     * @return array
     */
    public static function getAllCompaniesSources(): array
    {
        return [
            self::SOURCE_ORDERS_PER_COMPANY
        ];
    }
}
