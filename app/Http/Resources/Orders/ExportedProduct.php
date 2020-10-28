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

class ExportedProduct extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Order */
        return [
            "attr_color" => $this->productColor,
            "attr_model" => $this->productModel,
            "attr_options" => is_null($this->notes) ? "" : $this->notes,
            "attr_size" => $this->productSize,
            "categ_id" => [
                "id" => $this->productCategory->id,
                "name" => $this->productCategory->name
            ],
            "id" => 0,
            "list_price" => $this->offer->productDiscountedPrice,
            "name" => $this->productBrand. " " . $this->productModel,
            "product_brand_id" => [
                "id" => 0,
                "name" => $this->productBrand,
            ],
            "retail_price" => $this->offer->productListPrice,
            "seller_ids" => [new ExportedSupplier($this)]
        ];
    }
}
