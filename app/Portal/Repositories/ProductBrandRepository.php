<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\ProductBrand;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

/**
 * Class ProductBrandRepository
 *
 * @package App\Portal\Repositories
 * @method ProductBrand find(int $id, array $relations = [])
 */
class ProductBrandRepository extends BaseRepository
{
    /**
     * ProductBrandRepository constructor.
     *
     * @param ProductBrand $productBrand
     */
    public function __construct(ProductBrand $productBrand)
    {
        $this->model = $productBrand;
    }

    /**
     * @param array $data
     *
     * @return ProductBrand|false
     */
    public function create(array $data)
    {
        $brand = $this->model->newInstance();

        $brand->name = $data['name'];
        $brand->supplier_id = $data['supplier_id'] ?? AuthHelper::supplierId();

        return $brand->save() ? $brand : false;
    }

    /**
     * @return Collection|static[]
     */
    public function allFiltered(): Collection
    {
        $query = ProductBrand::query();
        return $query
            ->orderBy('name', 'asc')
            ->get();
    }
}
