<?php
namespace App\Partners\Controllers;

use App\Partners\Resources\UserResource;
use App\Partners\Models\Partner;
use App\Partners\Requests\PartnerInfoRequest;
use App\Partners\Requests\GenerateTokenRequest;
use App\Partners\Resources\PartnerResource;
use App\Partners\Services\TokenService;
use App\Portal\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PartnerController extends Controller
{
    public function generateToken(GenerateTokenRequest $request)
    {
        return response()->json(['token' => TokenService::generateToken(AuthHelper::user())]);
    }

    public function verifyToken(Request $request)
    {
        $token = $request->get('token');
        return response()->json(['valid' => TokenService::verifyToken($token)]);
    }

    public function getJwks()
    {
        return response()->json(TokenService::getJwks());
    }

    public function getPartnerInfo(PartnerInfoRequest $request, Partner $partner)
    {
        return response()->json(PartnerResource::make($partner));
    }

    public function getUserInfo()
    {
        $user = AuthHelper::user();
        return response()->json(UserResource::make($user));
    }
}
