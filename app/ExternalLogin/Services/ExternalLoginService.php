<?php
namespace App\ExternalLogin\Services;

use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Auth;
use App\ExternalLogin\Exceptions\AuthenticationError;
use App\Portal\Models\User;
use App\ExternalLogin\Exceptions\WrongRoleError;
use GuzzleHttp\Exception\ClientException;

class ExternalLoginService
{
    public static function validateLoginChallenge($challenge)
    {
        $loginValidation = HydraService::verifyLoginChallenge($challenge);
        if ($loginValidation->skip && !empty($loginValidation->subject)) {
            $user = User::find($loginValidation->subject);
            $acceptLogin = HydraService::acceptLogin($challenge, $user);
            if (isset($acceptLogin->redirect_to)) {
                $loginValidation->redirect_to = $acceptLogin->redirect_to;
            } else {
                $loginValidation->skip = false;
            }
        } else {
            $loginValidation->skip = false;
        }
        return $loginValidation;
    }

    public static function validateConsentChallenge($challenge)
    {
        $consentValidation = HydraService::verifyConsentChallenge($challenge);
        if ($consentValidation->skip && !empty($consentValidation->subject)) {
            $acceptLogin = HydraService::acceptConsent($challenge);
            if (isset($acceptLogin->redirect_to)) {
                $consentValidation->redirect_to = $acceptLogin->redirect_to;
            } else {
                $consentValidation->skip = false;
            }
        } else {
            $consentValidation->skip = false;
        }
        return $consentValidation;
    }

    public static function login($challenge, $email, $password)
    {
        $user = self::validateUser($email, $password);
        self::checkUserRole($user, $challenge);
        return HydraService::acceptLogin($challenge, $user);
    }

    public static function validateUser($email, $password): User
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];
        if (!Auth::validate($credentials)) {
            throw new AuthenticationError("The user or password are incorrect");
        }
        
        return User::where('email', $email)->firstOrFail();
    }

    public static function checkUserRole(User $user, $challenge)
    {
        if (!$user->isEmployee()) {
            try {
                HydraService::rejectLogin($challenge);
            } catch (Exception $error) {
                \Log::error($error);
            }
            throw new WrongRoleError();
        }
    }
}
