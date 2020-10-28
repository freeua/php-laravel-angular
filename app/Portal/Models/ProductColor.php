<?php

namespace App\Portal\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property integer $id
 * @property Supplier $supplier
 */
class ProductColor extends Model
{
    protected $table = 'product_colors';

    protected $fillable = [
        'name',
        'supplier_id'
    ];
}
