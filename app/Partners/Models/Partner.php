<?php
namespace App\Partners\Models;

use App\Models\Portal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property string name
 * @property string oauth_client_id
 * @property Collection portals
 */
class Partner extends Model
{
    protected $fillable = [
        'name',
        'oauth_client_id',
        'bike_configurator_url',
        'menu_text',
        'info_iframe_url',
    ];

    public static function findByClientId($oauthClientId)
    {
        return self::where('oauth_client_id', $oauthClientId)->firstOrFail();
    }

    public function portals(): HasMany
    {
        return $this->hasMany(Portal::class);
    }
}
