<?php

namespace App\Observers;

use App\Models\Portal;

class PortalObserver
{
    /**
     * Handle the portal "created" event.
     *
     * @param  Portal  $portal
     * @return void
     */
    public function created(Portal $portal)
    {
        $portal->code = $portal->generateCode($portal->id, 6, '', 'PE-');
        $portal->save();
    }
}
