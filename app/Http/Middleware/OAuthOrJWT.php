<?php
namespace App\Http\Middleware;

use App\ExternalLogin\Services\HydraService;
use App\Portal\Http\Middleware\VerifyJWT;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Partners\Models\Partner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Portal\Models\User;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\AlgorithmManagerFactory;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Namshi\JOSE\Signer\OpenSSL\ES512;

class OAuthOrJWT
{
    public function handle(Request $request, \Closure $next, $scopes)
    {
        $token = $request->bearerToken();

        if ($request->hasHeader('X-From-Benefit-Portal')) {
            return app(VerifyJWT::class)->handle($request, function ($request) use ($next) {
                return app(Authenticate::class)->handle($request, $next);
            });
        } else {
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

    private static function verifyJWT($token)
    {
        $algorithmManager = new AlgorithmManager([
            new HS256(),
        ]);
        $jwsVerifier = new JWSVerifier(
            $algorithmManager
        );

        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);

        $jws = $serializerManager->unserialize($token);

        $jwk = JWKFactory::createFromSecret(
            config('app.key'),       // The shared secret
            [                      // Optional additional members
                'alg' => 'HS256',
                'use' => 'sig'
            ]
        );

        return $jwsVerifier->verifyWithKey($jws, $jwk, 0);
    }
}
