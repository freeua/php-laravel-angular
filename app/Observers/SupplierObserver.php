<?php

namespace App\Observers;

use App\Portal\Models\Supplier;

class SupplierObserver
{
    /**
     * Handle the supplier "created" event.
     *
     * @param  Supplier  $supplier
     * @return void
     */
    public function created(Supplier $supplier)
    {
        $supplier->code = $supplier->generateCode($supplier->id, 6, '', 'LI-');
        $supplier->save();
    }
}
