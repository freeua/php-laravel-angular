<?php

declare(strict_types=1);

namespace App\Portal\Services\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Product;
use App\Portal\Repositories\ProductBrandRepository;
use App\Portal\Repositories\ProductModelRepository;
use App\Portal\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class ProductService
 *
 * @package App\Portal\Services\Supplier
 */
class ProductService
{
    /** @var ProductModelRepository */
    private $productModelRepository;
    /** @var ProductBrandRepository */
    private $productBrandRepository;
    /** @var ProductRepository */
    private $productRepository;

    /**
     * ProductService constructor.
     *
     * @param ProductRepository      $productRepository
     * @param ProductBrandRepository $productBrandRepository
     * @param ProductModelRepository $productModelRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductBrandRepository $productBrandRepository,
        ProductModelRepository $productModelRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->productModelRepository = $productModelRepository;
    }

    /**
     * @param array $params
     *
     * @return Product|false
     * @throws Exception
     */
    public function create(array $params)
    {
        DB::beginTransaction();

        try {
            if (empty($params['brand_id'])) {
                $params['brand_id'] = $this->productBrandRepository->create([
                    'name'        => $params['brand'],
                    'supplier_id' => $params['supplier_id'] ?? AuthHelper::supplierId()
                ])->id;
            }
            if (empty($params['model_id'])) {
                $params['model_id'] = $this->productModelRepository->create([
                    'name'        => $params['model'],
                    'supplier_id' => $params['supplier_id'] ?? AuthHelper::supplierId(),
                    'brand_id'    => $params['brand_id'],
                    'category_id' => $params['category_id']
                ])->id;
            }

            $product = $this->productRepository->create($params);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollback();

            Log::error('Create product error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return false;
        }
    }

    /**
     * @param array $params
     *
     * @return Product|false
     * @throws Exception
     */
    public function findOrCreate(array $params)
    {
        $product = null;

        if (!empty($params['brand']['id']) && !empty($params['model']['id'])) {
            $product = $this->productRepository->findWhere($params)->first();
        }

        return $product ?: $this->create($params);
    }
}
