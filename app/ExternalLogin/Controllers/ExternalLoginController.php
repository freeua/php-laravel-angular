<?php
namespace App\ExternalLogin\Controllers;

use Illuminate\Routing\Controller;
use App\ExternalLogin\Requests\ChallengeVerifyRequest;
use App\ExternalLogin\Resources\ChallengeVerifyResource;
use App\ExternalLogin\Resources\RedirectionResource;
use App\ExternalLogin\Services\HydraService;
use App\ExternalLogin\Services\ExternalLoginService;
use App\ExternalLogin\Requests\LoginRequest;

class ExternalLoginController extends Controller
{
    public function verifyLoginChallenge(ChallengeVerifyRequest $request)
    {
        return response()->json(
            ChallengeVerifyResource::make(ExternalLoginService::validateLoginChallenge($request->get('challenge')))
        );
    }

    public function verifyConsentChallenge(ChallengeVerifyRequest $request)
    {
        return response()->json(
            ChallengeVerifyResource::make(ExternalLoginService::validateConsentChallenge($request->get('challenge')))
        );
    }

    public function login(LoginRequest $request)
    {
        return response()->json(
            RedirectionResource::make(ExternalLoginService::login($request->get('challenge'), $request->get('email'), $request->get('password')))
        );
    }

    public function acceptConsent(string $consent)
    {
        return response()->json(
            RedirectionResource::make(HydraService::acceptConsent($consent)),
        );
    }

    public function rejectConsent(string $consent)
    {
        return response()->json(
            RedirectionResource::make(HydraService::rejectConsent($consent, 'User rejected the consent')),
        );
    }
}
