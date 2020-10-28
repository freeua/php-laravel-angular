<?php
namespace App\Partners\Resources;

use \Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'url' => $this->bike_configurator_url,
            'infoIframeUrl' => $this->info_iframe_url,
            'calculatorCid' => $this->calculator_cid,
            'menuText' => $this->configurator_menu_text,
            'id' => $this->id
        ];
    }
}
