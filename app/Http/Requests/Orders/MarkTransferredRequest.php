<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 2018-12-10
 * Time: 09:03
 */

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class MarkTransferredRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['numeric'];
    }
}
