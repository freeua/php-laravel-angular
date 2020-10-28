<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Class SearchRequest
 *
 * @package App\Http\Requests
 */
abstract class SearchRequest extends ApiRequest
{
    /**
     * @return array
     */
    abstract protected static function getCategories(): array;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search'   => 'required|string',
            'order_by' => 'string|nullable',
            'order'    => ['string', Rule::in(['asc', 'desc'])],
            'per_page' => 'integer|nullable',
            'page'     => 'integer|nullable'
        ];
    }
}
