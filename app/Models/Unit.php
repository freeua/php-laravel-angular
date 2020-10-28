<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Unit
 * @package App\Models
 * @property int    $id
 * @property string $name
 */
class Unit extends Model
{
    protected $fillable = ['name'];
}
