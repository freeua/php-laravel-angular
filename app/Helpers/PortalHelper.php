<?php

namespace App\Helpers;

use App\Models\Portal;

/**
 * Class PortalHelper
 *
 * @package App\Helpers
 */
class PortalHelper
{
    private static $portal;

    public static function id(): ?int
    {
        if (self::$portal) {
            return self::$portal->id;
        }
        return null;
    }

    public static function name(): ?string
    {
        if (self::$portal) {
            return self::$portal->name;
        }
        return 'Mercator Leasing';
    }

    public static function domain(): ?string
    {
        return self::$portal->domain;
    }
    
    public static function subdomain(): ?string
    {
        return self::$portal->subdomain;
    }

    public static function isActive(): ?bool
    {
        return self::$portal->status_id === Portal::STATUS_ACTIVE;
    }

    public static function frontendUrl(): ?string
    {
        return 'https://' . self::$portal->domain;
    }

    public static function getPortal() : ?Portal
    {
        return self::$portal;
    }

    public static function setPortal($portal): void
    {
        self::$portal = $portal;
    }
}
