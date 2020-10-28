<?php
namespace App\Http\Middleware;

use App\ExternalLogin\Services\HydraService;
use Illuminate\Http\Response;
use App\Partners\Models\Partner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Portal\Models\User;

class OAuth
{
    public function handle($request, \Closure $next, $scopes)
    {
        $token = $request->bearerToken();
        $verification = HydraService::verifyToken($token, $scopes);
        if ($verification != null) {
            try {
                if ($verification->sub == $verification->client_id) {
                    $request->requester = Partner::findByClientId($verification->client_id);
                } else {
                    $request->requester = User::findOrFail($verification->sub);
                }
            } catch (ModelNotFoundException $exception) {
                throw new \Exception("No partner or user is found with {$verification->sub} subject");
            }
            return $next($request);
        } else {
            return response()->json(['message' => 'Token invalid or inactive'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
