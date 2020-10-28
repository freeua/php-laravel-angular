<?php


namespace App\Helpers;

use App\Http\Requests\PaginationRequestTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class PaginationHelper
{
    const PAGE_SIZE = 25;
    public static function processList(Builder $query, PaginationRequestTransformer $pagination, \Closure $applyFilters = null, \Closure $applySearch = null)
    {
        if (!is_null($applyFilters)) {
            $query = $applyFilters($query, $pagination);
        }

        if (!empty($pagination->search) && !is_null($applyFilters)) {
            $query = $applySearch($query, $pagination->search);
        }

        if (!empty($pagination->orderBy) && !empty($pagination->order)) {
            $query->orderBy($pagination->orderBy, $pagination->order);
        }
        $pageSize = $pagination->pageSize ?? self::PAGE_SIZE;

        return $query->paginate($pageSize);
    }

    public static function paginationRequest(array $validation)
    {
        return array_merge([
            'orderBy'      => 'string|nullable',
            'order'         => ['string', Rule::in(['asc', 'desc']), 'nullable'],
            'pageSize'      => 'integer|nullable',
            'page'          => 'integer|nullable',
            'filterColumn' => 'string|nullable',
            'filterValue'  => 'string|nullable',
        ], $validation);
    }
}
