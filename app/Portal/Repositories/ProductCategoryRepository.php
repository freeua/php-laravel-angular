<?php

namespace App\Portal\Repositories;

use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Repositories\BaseRepository;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ProductCategoryRepository
 *
 * @package App\Portal\Repositories
 * @method ProductCategory find(int $id, array $relations = [])
 */
class ProductCategoryRepository extends BaseRepository
{
    /**
     * ProductCategoryRepository constructor.
     *
     * @param ProductCategory $productCategory
     */
    public function __construct(ProductCategory $productCategory)
    {
        $this->model = $productCategory;
    }

    public function allCompany(Company $company): Collection
    {
        $query = $this->newQuery();

        $query->select([
            'product_categories.*'
        ])
            ->join('company_product_categories', 'company_product_categories.category_id', '=', 'product_categories.id')
            ->where('company_product_categories.company_id', '=', $company->id)
            ->where('company_product_categories.status', '=', true)
        ;

        return $query->get();
    }
}
