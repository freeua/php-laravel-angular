<?php

namespace App\Portal\Http\Requests\V1\Employee;

use App\Portal\Services\Employee\SearchService;

/**
 * Class SearchRequest
 *
 * @package App\Portal\Http\Requests\V1\Employee
 */
class SearchRequest extends \App\Http\Requests\SearchRequest
{
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
     * @return array
     */
    protected static function getCategories(): array
    {
        return SearchService::getCategories();
    }
}
