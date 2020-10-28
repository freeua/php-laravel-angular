<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-09
 * Time: 21:44
 */

namespace App\Http\Resources\Orders;

use App\Portal\Models\Order;
use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportedSupplier extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Order */
        return [
            "city" => $this->supplierCity,
            "country_id" => [
                "id" => 58,
                "name" => "Deutschland"
            ],
            "credit_number" => "368925",
            "credit_state" => "positive",
            "email" => $this->supplier->admin_email,
            "email_type" => "business",
            "gender_id" => false,
            "id" => $this->id,
            "mobile" => false,
            "name" => $this->supplierName,
            "parent_id" => false,
            "personal_number" => false,
            "phone" => $this->supplier->phone,
            "ref" => $this->supplierCode,
            "state_id" => false,
            "street" => $this->supplierStreet,
            "street2" => "",
            "zip" => $this->supplierPostalCode
        ];
    }
}
