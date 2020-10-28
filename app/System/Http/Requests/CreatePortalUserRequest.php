<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\Portal\Models\User as PortalUser;
use Illuminate\Validation\Rule;

/**
 * Class CreatePortalUserRequest
 *
 * @package App\System\Http\Requests
 */
class CreatePortalUserRequest extends ApiRequest
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
            'email' => 'required|email|unique:portal_users,email,NULL,id,deleted_at,NULL,portal_id,' . $this->request->get('portal_id'),
            'status_id' => ['required', Rule::in([PortalUser::STATUS_ACTIVE, PortalUser::STATUS_INACTIVE])],
        ];
    }
}
