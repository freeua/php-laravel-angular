<?php

namespace App\Repositories;

use App\Portal\Models\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BaseOrderRepository
 *
 * @package App\Repositories
 * @method Order find(int $id, array $relations = [])
 */
class BaseOrderRepository extends BaseRepository
{
    /** @var Order */
    protected $model;
    /**
     * BaseOrderRepository constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query
            ->where(function (Builder $query) use ($search) {
                return $query
                    ->orWhereHas('company', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.brand', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('product.model', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function (Builder $query) use ($search) {
                        return $query
                            ->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                    ->orWhere('number', 'like', "%$search%");
            });
    }
}
