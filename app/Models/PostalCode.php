<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $city_id
 * @property string $code
 */
class PostalCode extends Model
{
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'city_id',
        'code'
    ];

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
