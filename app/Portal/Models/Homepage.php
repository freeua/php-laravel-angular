<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 10:55
 */

namespace App\Portal\Models;

use App\Portal\Http\Resources\V1\HomepageResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @property int        $id
 * @property string     $type
 * @property string     $items
 * @property int        $homepageable_id
 * @property string     $homepageable_type
 * @property string     $logo
 */
class Homepage extends PortalModel
{
    const PORTAL_DEFAULT_HOMEPAGE = 'portal_default_homepage';
    const PORTAL_HOMEPAGE = 'portal_homepage';
    const SUPPLIER_DEFAULT_HOMEPAGE = 'supplier_default_homepage';
    const SUPPLIER_HOMEPAGE = 'supplier_homepage';
    const COMPANY_DEFAULT_HOMEPAGE = 'company_default_homepage';
    const PORTAL_COMPANY_DEFAULT_HOMEPAGE = 'portal_company_default_homepage';
    const COMPANY_HOMEPAGE = 'company_homepage';
    const EMPLOYEE_DEFAULT_HOMEPAGE = 'employee_default_homepage';
    const PORTAL_EMPLOYEE_DEFAULT_HOMEPAGE = 'portal_employee_default_homepage';
    const EMPLOYEE_HOMEPAGE = 'employee_homepage';

    /**
     * @var array
     */
    protected $fillable = [
        'type',
        'items',
        'homepageable_id',
        'homepageable_type',
    ];

    protected $casts = [
        'items' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function homepageable()
    {
        return $this->morphTo();
    }

    public static function getDefaultHomepageByType($type)
    {
        try {
            $homepage = self::where('type', $type)->firstOrFail();

            return new HomepageResource($homepage);
        } catch (ModelNotFoundException $e) {
            return ['items'=>null];
        }
    }
}
