<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 11:19
 */

namespace App\Portal\Http\Controllers\V1;

use App\Helpers\PortalHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\HomepageRequest;
use App\Portal\Http\Resources\V1\HomepageResource;
use App\Portal\Models\Homepage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HomepageController extends Controller
{
    public function get()
    {
        $portal = PortalHelper::getPortal();
        if (\Cache::has(Homepage::PORTAL_HOMEPAGE.'_'.$portal->id)) {
            return response()->success(\Cache::get(Homepage::PORTAL_HOMEPAGE.'_'.$portal->id));
        } else {
            if ($portal->homepage) {
                $response = new HomepageResource($portal->homepage);
                \Cache::put(Homepage::PORTAL_HOMEPAGE.'_'.$portal->id, $response, 18000);
                return response()->success($response);
            } else {
                $response = new HomepageResource(Homepage::getDefaultHomepageByType(Homepage::PORTAL_DEFAULT_HOMEPAGE));
                \Cache::put(Homepage::PORTAL_HOMEPAGE.'_'.$portal->id, $response, 18000);
                return response()->success($response);
            }
        }
    }
}
