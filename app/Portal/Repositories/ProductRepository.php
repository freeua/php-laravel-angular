<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Product;
use App\Portal\Models\ProductColor;
use App\Portal\Models\ProductSize;
use App\Repositories\BaseRepository;

/**
 * Class ProductRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Product find(int $id, array $relations = [])
 */
class ProductRepository extends BaseRepository
{

    /**
     * ProductRepository constructor.
     *
     * @param Product                  $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * @param array $data
     *
     * @return Product|false
     */
    public function create(array $data)
    {
        $product = $this->model->newInstance();

        $product->code = $data['code'] ?? $this->generateCode('', 0, 12);
        $product->supplier_id = $data['supplier_id'] ?? AuthHelper::supplierId();
        $product->category_id = $data['category_id'];
        $product->model_id = $data['model_id'];
        $product->brand_id = $data['brand_id'];
        $product->image = $data['image'] ?? null;
        $product->color = $data['color'];
        ProductColor::updateOrCreate([
            'name' => $product->color,
        ]);
        $product->size = $data['size'];
        ProductSize::updateOrCreate([
            'name' => $product->size,
        ]);

        if (!$product->save()) {
            return false;
        }

        if (!empty($data['attributes'])) {
            $attributes = $product->attributes()->pluck('slug', 'id');

            foreach ($data['attributes'] as $attribute) {
                if (empty($attribute['slug'])) {
                    $attribute['slug'] = $attributes[$attribute['id']];
                } elseif (empty($attribute['id'])) {
                    $attribute['id'] = $attributes->search($attribute['slug']);
                }
                $product->{$attribute['slug']} = $attribute['value'];
            }
            $product->save();
        }

        return $product;
    }

    /**
     * @inheritdoc
     */
    public function findWhere(array $where, array $relationships = [])
    {
        $query = Product::query();


        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        return $query
            ->get();
    }
}
