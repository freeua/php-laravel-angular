<?php
namespace App\Portals\Controllers;

use App\Portals\Resources\BasicPortalResource;
use Illuminate\Routing\Controller;
use App\Helpers\PortalHelper;

class PortalController extends Controller
{
    public function getCurrent()
    {
        return response()->json(BasicPortalResource::make(PortalHelper::getPortal()));
    }
}
