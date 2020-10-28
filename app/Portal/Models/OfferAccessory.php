<?php

namespace App\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OfferAccessory
 * @package App
 * @property int        $id
 * @property string     $name
 * @property double     $amount
 * @property double     $price
 * @property double     $discount
 * @property double     $total
 */
class OfferAccessory extends Model
{

    protected $fillable = [
        'name',
        'amount',
        'price',
        'discount',
        'discounted_price',
    ];

    protected $appends = [
        'total',
    ];

    public function getTotalAttribute()
    {
        return ($this->amount * $this->price) * (1 - $this->discount / 100);
    }
}
