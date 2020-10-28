<?php

namespace App\Portal\Models;

use App\Models\Portal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Setting
 *
 * @package App\Portal\Models
 * @property int    $id
 * @property Portal $portal
 * @property string $key
 * @property string $value
 * @property int    $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends PortalModel
{
    /**
     * @var string The name of the table.
     * It exists a System model with table name "settings" so we
     * change it to suffixed portal_
     */
    protected $table = 'portal_settings';

    public function portal() : BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
