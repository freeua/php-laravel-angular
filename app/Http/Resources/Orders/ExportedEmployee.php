<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-09
 * Time: 21:44
 */

namespace App\Http\Resources\Orders;

use App\Portal\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportedEmployee extends JsonResource
{
    public function toArray($request)
    {
        $userContractData = isset($this->offer->contract_data['user']) ? $this->offer->contract_data['user'] : [];
        /** @var $this Order */
        return [
            "city" => $this->employeeCity,
            "country_id" => [
                "id" => 58,
                "name" => "Deutschland"
            ],
            "credit_comment" => false,
            "credit_number" => false,
            "credit_state" => false,
            "email" => $this->employeeEmail,
            "email_type" => "private",
            "fax" => false,
            "gender_id" => [
                "id" => 0,
                "name" => $this->employeeSalutation,
            ],
            "id" => $this->user->id,
            "mobile" => false,
            "name" => $this->employeeName,
            "personal_number" => $this->employeeNumber,
            "phone" => $this->employeePhone,
            "ref" => false,
            "state_id" => false,
            "street" => $this->employeeStreet,
            "street2" => false,
            "zip" => $this->employeePostalCode
        ];
    }
}
