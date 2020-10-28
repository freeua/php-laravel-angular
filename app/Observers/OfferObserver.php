<?php

namespace App\Observers;

use App\Portal\Models\Offer;

class OfferObserver
{
    /**
     * Handle the offer "created" event.
     *
     * @param  Offer  $offer
     * @return void
     */
    public function created(Offer $offer)
    {
        $offer->number = $offer->generateCode($offer->id, 7, '', 'ANG-');
        $offer->save();
    }
}
