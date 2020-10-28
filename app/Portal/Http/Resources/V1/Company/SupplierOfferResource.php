<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 07.03.2019
 * Time: 14:34
 */

namespace App\Portal\Http\Resources\V1\Company;

use App\Portal\Http\Resources\V1\ContractSimpleResource;
use App\Portal\Http\Resources\V1\OrderSimpleResource;
use App\Portal\Http\Resources\V1\ProductResource;
use App\Portal\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierOfferResource
 * @package App\Portal\Http\Resources\V1\Company
 */
class SupplierOfferResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this Offer */
        return [
            'id' => $this->id,
            'number' => $this->number,
            'product_id' => $this->product_id,
            'productModel' => $this->productModel,
            'productBrand' => $this->productBrand,
            'supplierName' => $this->supplierName,
            'status' => $this->status,
            'order' => new OrderSimpleResource($this->order),
            'contract' => $this->order ? new ContractSimpleResource($this->order->contract) : null,
        ];
    }
}
