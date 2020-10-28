<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User as PortalUser;
use Illuminate\Validation\Rule;

/**
 * Class UpdatePortalUserRequest
 *
 * @package App\System\Http\Requests
 */
class UpdatePortalUserRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'portal_id' => 'required|integer|exists:portals,id,deleted_at,NULL',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'hasEditPermission' => 'required|boolean',
            'status_id' => ['required', Rule::in([PortalUser::STATUS_ACTIVE, PortalUser::STATUS_INACTIVE])],
        ];
    }
}
