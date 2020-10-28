<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-09
 * Time: 21:44
 */

namespace App\Http\Resources\Orders;

use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportedInvoice extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Order */
        $contractPrices = new ContractPrices($this->offer);
        /** @var $this Order */
        return [
            'amount_untaxed' => $contractPrices->getNetTotal()->getAmount()->toFloat(),
            'amount_tax' => round($contractPrices->getVatApplied()->getAmount()->toFloat(), 2),
            'amount_total' => round($this->offer->agreedPurchasePrice, 2),
            'currency_id' => [
                'id' => 1,
                'name' => 'EUR',
            ],
            'date_invoice' => $this->date->format('Y-m-d H:i:s'),
            'id' => $this->id,
            'internal_number' => $this->number,
            'kre' => $this->invoiceFile ? "/system-api/export/orders/{$this->id}/attachment/kre" : false,
            'mlf_bp_number' => $this->supplier_id, // TODO
            'mlf_transfer_state' => 'new',
            'mlf_transmission_state' => $this->transferred ? 'fetched' : 'provided',
            'mlf_transmission_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'origin' => $this->number,
            'partner_id' => [
                'city' => "Schweinfurt",
                'country_id' => [
                    'id' => 58,
                    'name' => "Deutschland"
                ],
                'credit_comment' => false,
                'credit_number' => false,
                'credit_state' => false,
                'email' => false,
                'email_type' => false,
                'fax' => "09721 / 47 47 290",
                'gender_id' => [
                    'id' => 1,
                    'name' => "Herr",
                ],
                'id' => 70923,
                'mobile' => false,
                'name' => "MLF Mercator Leasing GmbH & Co. Finanz-KG",
                'parent_id' => false,
                'personal_number' => false,
                'phone' => "09721 / 47 47-0",
                'ref' => "PAR-000635",
                'state_id' => false,
                'street' => "Londonstraße 1",
                'street2' => "",
                'zip' => "97424",
            ],
            'payment_term' => false,
            'residual' => $this->contract->calculatedResidualValue,
        ];
    }
}
