<?php

namespace App\Helpers;

use App\Models\Text;
use Illuminate\Support\Str;
use App\Helpers\PortalHelper;

/**
 * Class PortalHelper
 *
 * @package App\Helpers
 */
class TextHelper
{
    private static function getResource(Text $text)
    {
        /* @var $text Text */
        return [
            'id'   => $text->id,
            'key' => $text->data['key'],
            'description' => $text->data['description'],
            'title' => $text->data['title'],
            'subtitle' => $text->data['subtitle'],
            'portalId' => $text->portal_id
        ];
    }
    
    private static function getGlobalData($collection)
    {
        $globalPortalData = $collection->filter(function ($item, $key) {
            return $item->portal_id === null && Str::startsWith($item->data['key'], 'portal');
        });
        $globalCompanyData = $collection->filter(function ($item, $key) {
            return $item->portal_id === null && Str::startsWith($item->data['key'], 'firma');
        });
        $globalEmployeeData = $collection->filter(function ($item, $key) {
            return $item->portal_id === null && Str::startsWith($item->data['key'], 'mitarbeiter');
        });
        $globalLieferantenData = $collection->filter(function ($item, $key) {
            return $item->portal_id === null && Str::startsWith($item->data['key'], 'lieferanten');
        });
        return array($globalPortalData, $globalCompanyData, $globalEmployeeData, $globalLieferantenData);
    }

    private static function getPortalData($collection)
    {
        list($globalPortalData, $globalCompanyData, $globalEmployeeData, $globalLieferantenData) = self::getGlobalData($collection);
        $portalCompanyData = $collection->filter(function ($item, $key) {
            return $item->portal_id === PortalHelper::id() && Str::startsWith($item->data['key'], 'firma');
        });
        $portalEmployeeData = $collection->filter(function ($item, $key) {
            return $item->portal_id === PortalHelper::id() && Str::startsWith($item->data['key'], 'mitarbeiter');
        });
        $portalLieferantenData = $collection->filter(function ($item, $key) {
            return $item->portal_id === PortalHelper::id() && Str::startsWith($item->data['key'], 'lieferanten');
        });
        $diffGlobalCompanyData = $globalCompanyData->merge($portalCompanyData)->sortBy('data.key')->sortByDesc('id')->unique('data.key');
        $diffGlobalEmployeeData = $globalEmployeeData->merge($portalEmployeeData)->sortBy('data.key')->sortByDesc('id')->unique('data.key');
        $diffGlobalLieferantenData = $globalLieferantenData->merge($portalLieferantenData)->sortBy('data.key')->sortByDesc('id')->unique('data.key');
        $diffGlobalCompanyData = $diffGlobalCompanyData->transform(function ($text) {
            return self::getResource($text);
        })->values();
        $diffGlobalEmployeeData = $diffGlobalEmployeeData->transform(function ($text) {
            return self::getResource($text);
        })->values();
        $globalPortalData = $globalPortalData->transform(function ($text) {
            return self::getResource($text);
        })->values();
        $diffGlobalLieferantenData = $diffGlobalLieferantenData->transform(function ($text) {
            return self::getResource($text);
        })->values();
        return array($diffGlobalCompanyData, $diffGlobalEmployeeData, $globalPortalData, $diffGlobalLieferantenData);
    }

    public static function getSystemCollection($collection): Array
    {
        list($globalPortalData, $globalCompanyData, $globalEmployeeData, $globalLieferantenData) = self::getGlobalData($collection);
        return array(
            array(
                'name' => 'Portal',
                'children' => $globalPortalData->transform(function ($text) {
                    return self::getResource($text);
                })->sortBy('key')->values()
            ),
            array(
                'name' => 'Firma',
                'children' => $globalCompanyData->transform(function ($text) {
                    return self::getResource($text);
                })->sortBy('key')->values()
            ),
            array(
                'name' => 'Mitarbeiter',
                'children' => $globalEmployeeData->transform(function ($text) {
                    return self::getResource($text);
                })->sortBy('key')->values()
            ),
            array(
                'name' => 'Lieferanten',
                'children' => $globalLieferantenData->transform(function ($text) {
                    return self::getResource($text);
                })->sortBy('key')->values()
            )
        );
    }

    public static function getPortalCollection($collection): Array
    {
        list($diffGlobalCompanyData, $diffGlobalEmployeeData, $diffGlobalPortalData, $diffGlobalLieferantenData) = self::getPortalData($collection);
        return array(
            array(
                'name' => 'Firma',
                'children' => $diffGlobalCompanyData->sortBy('key')->values()
            ),
            array(
                'name' => 'Mitarbeiter',
                'children' => $diffGlobalEmployeeData->sortBy('key')->values()
            ),
            array(
                'name' => 'Lieferanten',
                'children' => $diffGlobalLieferantenData->sortBy('key')->values()
            )
        );
    }

    public static function getSettingsCollection($collection): Array
    {
        list($companyData, $employeeData, $portalData, $lieferantenData) = self::getPortalData($collection);
        $portalDataMap = $portalData->reduce(function ($keyLookup, $text) {
            $keyLookup[$text['key']] = $text;
            return $keyLookup;
        }, []);
        $companyDataMap = $companyData->reduce(function ($keyLookup, $text) {
            $keyLookup[$text['key']] = $text;
            return $keyLookup;
        }, []);
        $employeeDataMap = $employeeData->reduce(function ($keyLookup, $text) {
            $keyLookup[$text['key']] = $text;
            return $keyLookup;
        }, []);
        $lieferantenDataMap = $lieferantenData->reduce(function ($keyLookup, $text) {
            $keyLookup[$text['key']] = $text;
            return $keyLookup;
        }, []);
        $settingsCollection = array_merge($portalDataMap, $companyDataMap, $employeeDataMap, $lieferantenDataMap);
        return $settingsCollection;
    }
}
