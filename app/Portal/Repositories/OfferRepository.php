<?php

namespace App\Portal\Repositories;

use App\Portal\Models\Offer;
use App\Helpers\PortalHelper;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OfferRepository
 *
 * @package App\Portal\Repositories
 *
 * @method OfferRepository find(int $id, array $relations = [])
 */
class OfferRepository extends BaseRepository
{
    /** @var Offer */
    protected $model;

    /**
     * OfferRepository constructor.
     *
     * @param Offer $offer
     */
    public function __construct(Offer $offer)
    {
        $this->model = $offer;
    }

    /**
     * @param string $status
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|\Illuminate\Support\Collection
     */
    public function getExpired(string $status = Offer::STATUS_PENDING)
    {
        return $this
            ->findWhere([['expiry_date', '<', Carbon::now()->toDateString()], ['status_id', '=', $status]])
            ->where('offers.portal_id', '=', PortalHelper::id());
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            return $query
                ->orWhere('supplier_name', 'like', "%$search%")
                ->orWhere('product_model', 'like', "%$search%")
                ->orWhere('product_brand', 'like', "%$search%")
                ->orWhere('number', 'like', "%$search%")
                ->orWhere('employee_name', 'like', "%$search%");
        });
    }
}
