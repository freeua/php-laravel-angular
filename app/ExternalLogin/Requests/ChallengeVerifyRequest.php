<?php
namespace App\ExternalLogin\Requests;

use App\Http\Requests\ApiRequest;

class ChallengeVerifyRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'challenge' => 'required|string',
        ];
    }
}
