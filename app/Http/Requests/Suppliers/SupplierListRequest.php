<?php
namespace App\Http\Requests\Suppliers;

use App\Http\Requests\ApiRequest;
use App\Portal\Helpers\AuthHelper;

class SupplierListRequest extends ApiRequest
{
    public function authorize()
    {
        $user = AuthHelper::user();
        return $user && ($user->isEmployee() || $user->isCompanyAdmin() || $user->isAdmin());
    }

    public function rules()
    {
        return [];
    }
}
