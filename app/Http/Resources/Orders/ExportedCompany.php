<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-09
 * Time: 21:44
 */

namespace App\Http\Resources\Orders;

use App\Models\Companies\Company;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportedCompany extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Company */
        return [
            "city" => $this->city->name,
            "country_id" => [
                "id" => 58,
                "name" => "Deutschland"
            ],
            "credit_number" => $this->boni_number,
            "credit_state" => "positive", // TODO
            "email" => $this->admin_email,
            "email_type" => "business",
            "fax" => false,
            "id" => $this->id,
            "mobile" => false,
            "name" => $this->name,
            "parent_id" => false,
            "personal_number" => false,
            "phone" => $this->phone,
            "ref" => $this->code,
            "state_id" => false,
            "street" => $this->address,
            "street2" => "",
            "zip" => $this->zip
        ];
    }
}
