<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 26/09/2018
 * Time: 16:27
 */

namespace App\Traits;

use App\Models\Status;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

trait HasStatus
{

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }


    /**
     * @return array
     */
    public static function getStatuses(): Collection
    {
        $table = app(get_called_class())->getTable();
        return Status::query()
            ->where('table', '=', $table)->get();
    }
}
