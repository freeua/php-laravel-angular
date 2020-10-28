<?php

declare(strict_types=1);

namespace App\Portal\Services;

use App\Portal\Models\Offer;
use App\Portal\Repositories\OfferRepository;
use Carbon\Carbon;

/**
 * Class OfferService
 *
 * @package App\Portal\Services
 */
class OfferService
{
    /** @var OfferRepository */
    private $offerRepository;

    /**
     * ProductService constructor.
     *
     * @param OfferRepository $offerRepository
     */
    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    /**
     * @return int|false
     */
    public function rejectExpired()
    {
        $offers = $this->offerRepository->getExpired();

        if (!$offers) {
            return 0;
        }

        $ids = $offers->pluck('id')->toArray();

        $updated = $this->offerRepository->butchUpdate($ids, ['status' => Offer::STATUS_REJECTED, 'status_updated_at' => Carbon::now()]);

        if (!$updated) {
            return false;
        }

        return count($ids);
    }
}
