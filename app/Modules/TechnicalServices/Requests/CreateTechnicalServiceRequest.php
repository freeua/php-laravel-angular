<?php

namespace App\Modules\TechnicalServices\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;

/**
 * Class TechnicalRequestAcceptRequest
 *
 * @package App\Portal\Http\Requests\V1\Supplier
 */
class CreateTechnicalServiceRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = AuthHelper::user();
        $contract = request()->route('contract');
        if ($user->id === $contract->user_id) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
