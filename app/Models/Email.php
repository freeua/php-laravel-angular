<?php

namespace App\Models;

use App\Portal\Helpers\AuthHelper;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = ['subject', 'body', 'company_id'];
    protected $guarded = ['key', 'vars'];
}
