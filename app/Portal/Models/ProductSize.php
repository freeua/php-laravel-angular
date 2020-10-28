<?php

namespace App\Portal\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property integer $id
 * @property Supplier $supplier
 */
class ProductSize extends Model
{
    protected $table = 'product_sizes';

    protected $fillable = [
        'name',
        'supplier_id'
    ];
}
