<?php
namespace App\Http\Middleware;

use App\Partners\Services\TokenService;
use App\Portal\Models\User;
use Illuminate\Support\Facades\Auth;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\ES512;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class PartnerJWT
{
    public function handle($request, \Closure $next)
    {
        $token = $request->bearerToken();
        $jws = self::verifyJWT($token);
        if (!is_null($jws)) {
            $user = User::find($jws['sub']);
            Auth::login($user);
            return $next($request);
        }
        throw new TokenInvalidException();
    }

    private static function verifyJWT($token) : array
    {
        $algorithmManager = new AlgorithmManager([
            new ES512(),
        ]);
        $jwsVerifier = new JWSVerifier(
            $algorithmManager
        );
        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);

        $jws = $serializerManager->unserialize($token);
        if (!$jws->getSignature(0)->hasProtectedHeaderParameter('kid')) {
            return null;
        }

        $jwk = TokenService::getJwk($jws->getSignature(0)->getProtectedHeaderParameter('kid'));
        if ($jwsVerifier->verifyWithKey($jws, $jwk, 0)) {
            return json_decode($jws->getPayload(), true);
        }
        return null;
    }
}
