<?php
namespace App\ExternalLogin\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use App\ExternalLogin\Exceptions\HydraConnectionException;
use GuzzleHttp\Exception\ClientException;
use App\ExternalLogin\Exceptions\HydraException;
use App\Portal\Models\User;
use App\ExternalLogin\Resources\UserResource;
use App\Partners\Models\Partner;

class HydraService
{
    public static function verifyLoginChallenge($challenge)
    {
        try {
            $response = self::makeJsonRequest('GET', "/oauth2/auth/requests/login?login_challenge=$challenge");
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }

        return json_decode($response->getBody());
    }

    public static function verifyConsentChallenge($challenge)
    {
        try {
            $response = self::makeJsonRequest('GET', "/oauth2/auth/requests/consent?consent_challenge=$challenge");
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }

        return json_decode($response->getBody());
    }

    public static function acceptLogin($challenge, User $user)
    {
        $request = [
            'subject' => (string)$user->id,
            'remember' => true,
            'remember_for' => 0,
        ];
        try {
            $response = self::makeJsonRequest('PUT', "/oauth2/auth/requests/login/accept?login_challenge=$challenge", $request);
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }
        return json_decode($response->getBody());
    }

    public static function rejectLogin($challenge)
    {
        try {
            $response = self::makeJsonRequest('PUT', "/oauth2/auth/requests/login/reject?login_challenge=$challenge");
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }
        return json_decode($response->getBody());
    }

    public static function acceptConsent($challenge)
    {
        $consentResponse = self::verifyConsentChallenge($challenge);
        $user = User::findOrFail($consentResponse->subject);
        $request = [
            'remember' => true,
            'remember_for' => 0,
            'grant_scope' => ['openid'],
            'session' => [
                'id_token' => UserResource::make($user),
            ],
        ];
        try {
            $response = self::makeJsonRequest('PUT', "/oauth2/auth/requests/consent/accept?consent_challenge=$challenge", $request);
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }
        return json_decode($response->getBody());
    }

    public static function rejectConsent($challenge, $error, $errorDescription = null)
    {
        $request = [
            'error' => $error,
            'error_description' => isset($errorDescription) ? $errorDescription : $error,
        ];
        try {
            $response = self::makeJsonRequest('PUT', "/oauth2/auth/requests/consent/reject?consent_challenge=$challenge", $request);
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }
        return json_decode($response->getBody());
    }

    public static function verifyToken($token, $scopes): ?\stdClass
    {
        try {
            $payload = [
                'token' => $token,
                'scopes' => $scopes,
            ];
            $response = self::makeFormRequest('POST', '/oauth2/introspect', $payload);
            $responseJson = json_decode($response->getBody());
            if (isset($responseJson->active) && $responseJson->active) {
                return $responseJson;
            }
            return null;
        } catch (ClientException $exception) {
            throw new HydraException($exception);
        }
        return null;
    }

    private static function makeJsonRequest(string $method, string $path, ?array $payload = null, array $options = [])
    {
        $options['json'] = $payload;
        return self::makeRequest($method, $path, $options);
    }

    private static function makeFormRequest(string $method, string $path, ?array $payload = null, array $options = [])
    {
        $options['form_params'] = $payload;
        return self::makeRequest($method, $path, $options);
    }
    
    private static function makeRequest(string $method, string $path, array $options)
    {
        $hydraSocket = env('HYDRA_SOCKET', '/opt/hydra/admin.sock');
        $client = new Client();
        try {
            $options = array_merge($options, [
                'curl' => [
                    CURLOPT_UNIX_SOCKET_PATH => $hydraSocket,
                ],
                'base_uri' => 'http://localhost', // we are connecting through socket. Domain doesn't matter (https://github.com/curl/curl/issues/936)];
            ]);
            $request = $client->request(
                $method,
                $path,
                $options,
            );
        } catch (ConnectException $exception) {
            \Log::critical("Unable to connect to hydra socket " . $hydraSocket);
            throw new HydraConnectionException($exception);
        }

        return $request;
    }
}
