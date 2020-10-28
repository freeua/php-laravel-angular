<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\ProductModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

/**
 * Class ProductModelRepository
 *
 * @package App\Portal\Repositories
 * @method ProductModel find(int $id, array $relations = [])
 */
class ProductModelRepository extends BaseRepository
{
    /**
     * ProductModelRepository constructor.
     *
     * @param ProductModel $productModel
     */
    public function __construct(ProductModel $productModel)
    {
        $this->model = $productModel;
    }

    /**
     * @param array $data
     *
     * @return ProductModel|false
     */
    public function create(array $data)
    {
        $model = $this->model->newInstance();

        $model->name = $data['name'];
        $model->supplier_id = $data['supplier_id'] ?? AuthHelper::supplierId();
        $model->brand_id = $data['brand_id'];
        $model->category_id = $data['category_id'];

        return $model->save() ? $model : false;
    }

    /**
     * @param array $params
     *
     * @return Collection|static[]
     */
    public function allFiltered(array $params): Collection
    {
        $query = $this->newQuery();


        if (isset($params['brand'])) {
            $query->whereHas('brand', function ($query) use ($params) {
                $query->where('name', $params['brand']);
            });
        }

        return $query->orderBy('name', 'asc')->get();
    }
}
