<?php

namespace App\System\Http\Requests;

use App\Http\Requests\ApiRequest;
use App\System\Services\SearchService;
use Illuminate\Validation\Rule;

/**
 * Class SearchRequest
 *
 * @package App\System\Http\Requests
 */
class SearchRequest extends \App\Http\Requests\SearchRequest
{
    const CATEGORY_ORDERS = 'orders';

    const CATEGORY_CONTRACTS = 'contracts';

    const CATEGORY_USERS = 'users';

    const CATEGORY_SUPPLIERS = 'suppliers';

    const CATEGORY_PORTALS = 'portals';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_USERS,
            self::CATEGORY_SUPPLIERS,
            self::CATEGORY_PORTALS,
            self::CATEGORY_ORDERS,
            self::CATEGORY_CONTRACTS
        ];
    }
}
