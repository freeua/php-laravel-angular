<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Helpers\PortalHelper;

class Text extends Model
{
    const STATUS_PORTAL = 1;
    const STATUS_COMPANY = 2;
    const STATUS_EMPLOYEE = 3;
    const CACHE_KEY = 'texts';
    const CACHE_PORTAL_KEY = 'portal_PORTALID_Texts';
    const CACHE_COLLECTION_KEY = 'settingsCollection';
    const CACHE_PORTAL_COLLECTION_KEY = 'portal_PORTALID_SettingsCollection';

    protected $fillable = ['data', 'portal_id'];
    protected $table = 'texts';
    protected $casts = [
        'data' => 'array'
    ];

    static function getCacheKey()
    {
        if (PortalHelper::id()) {
            return self::getPortalKey(self::CACHE_PORTAL_KEY);
        }
        return self::CACHE_KEY;
    }

    static function getCacheCollectionKey()
    {
        if (PortalHelper::id()) {
            return self::getPortalKey(self::CACHE_PORTAL_COLLECTION_KEY);
        }
        return self::CACHE_COLLECTION_KEY;
    }
    
    private static function getPortalKey($constant, $portalId = null)
    {
        $portalId = (!$portalId) ? PortalHelper::id() : $portalId;
        return str_replace('PORTALID', $portalId, $constant);
    }
    
    static function resetCaches(Collection $portalIds)
    {
        \Cache::forget(self::CACHE_KEY);
        \Cache::forget(self::CACHE_COLLECTION_KEY);
        foreach ($portalIds as $portalId) {
            \Cache::forget(self::getPortalKey(self::CACHE_PORTAL_KEY, $portalId));
            \Cache::forget(self::getPortalKey(self::CACHE_PORTAL_COLLECTION_KEY, $portalId));
        }
    }
}
