<?php

namespace App\Http\Resources\Orders;

use App\Helpers\StorageHelper;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Order;
use Brick\Math\RoundingMode;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;

class ExportedOrder extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        Resource::withoutWrapping();
        /** @var $this Order */
        $contractPrices = new ContractPrices($this->offer);
        /** @var $this Order */
        return [
            'amount_untaxed' => $contractPrices->getNetTotal()->getAmount()->toFloat(),
            'amount_tax' => round($contractPrices->getVatApplied()->getAmount()->toFloat(), 2),
            'amount_total' => round($this->offer->agreedPurchasePrice, 2),
            'name' => $this->number,
            'client_order_ref' => false,
            'client_order_references' => new \stdClass(),
            'date_order' => $this->date->format('Y-m-d H:i:s'),
            'elv' => "/system-api/export/orders/{$this->id}/attachment/elv",
            'extra' => false,
            'fha' => $this->supplierOfferFile ? "/system-api/export/orders/{$this->id}/attachment/fha" : false,
            'id' => $this->id,
            "order_state" => "progress",
            "uev_signed" => "/system-api/export/orders/{$this->id}/attachment/uev_signed",
            'invoice' => new ExportedInvoice($this),
            'leasing_id' => new ExportedLeasingSettings($this),
            "partner_id" => new ExportedCompany($this->company),
            "partner_shipping_id" => new ExportedEmployee($this),
            "product" => new ExportedProduct($this),
            "source_id" => [
                "id" => 9,
                "name" => "Dealer Portal"
            ],
            "stock_picking" => [[
                "date" => $this->pickedUpAt->format('Y-m-d H:i:s'),
                "date_done" => $this->pickedUpAt->format('Y-m-d H:i:s'),
                "id" => $this->id,
                "origin" => $this->number,
                "partner_id" => false,
                "serial_number" => false, // TODO Know the serial number
                "ueb" => "/system-api/export/orders/{$this->id}/attachment/ueb"
            ]],
        ];
    }
}
