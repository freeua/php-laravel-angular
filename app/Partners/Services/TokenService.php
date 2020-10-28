<?php

namespace App\Partners\Services;

use App\Partners\Models\PartnerToken;
use App\Portal\Models\User;
use Illuminate\Support\Arr;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES512;
use Jose\Component\Signature\Algorithm\PS512;
use Jose\Component\Signature\Algorithm\RS512;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Ramsey\Uuid\Uuid;

class TokenService
{
    public static function generateToken(User $user)
    {

        $algorithmManager = new AlgorithmManager([
            new ES512(),
        ]);
        if (!\Cache::has('jwk-partner')) {
            self::getJwks();
        }
        $jwkConfig = \Cache::get('jwk-partner');

        // Our key.
        $jwk = JWKFactory::createFromValues($jwkConfig);

        // We instantiate our JWS Builder.
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder
            ->create()
            ->withPayload(self::generatePayload($user))
            ->addSignature($jwk, [
                'alg' => 'ES512',
                'kid' => $jwk->get('kid'),
            ])
            ->build();
        $serializer = new CompactSerializer();

        return $serializer->serialize($jws);
    }

    public static function getJwks()
    {
        return \Cache::remember('jwks-partner', 48000, function () {

            $jwk = JWKFactory::createECKey('P-521', [
                'kid' => Uuid::uuid4()->serialize(),
            ]);
            $partnerToken = new PartnerToken([
                'id' => $jwk->get('kid'),
                'key' => json_encode($jwk->jsonSerialize()),
            ]);
            $partnerToken->saveOrFail();
            \Cache::put('jwk-partner', $jwk->jsonSerialize(), 48000);
            return JWKSet::createFromKeyData(['keys' => [$jwk->toPublic()->all()]]);
        });
    }

    public static function getJwk($kid) : JWK
    {
        return JWKFactory::createFromValues(json_decode(PartnerToken::findByKeyId($kid)->key, true));
    }

    public static function verifyToken($token)
    {
        $jwks = JWKSet::createFromJson(json_encode(self::getJwks()));
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

        $jwk = self::getJwk($jws->getSignature(0)->getProtectedHeaderParameter('kid'));

        return $jwsVerifier->verifyWithKey($jws, $jwk, 0);
    }

    private static function generatePayload(User $user)
    {
        $userData = Arr::only($user->toArray(), ['id', 'name', 'companyId']);
        $payload = json_encode(array_merge([
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'iss' => 'Mercator API',
            'aud' => 'Benefit Portal',
            'sub' => $user->id,
        ], $userData));
        return $payload;
    }
}
