<?php

namespace App\System\Services;

use App\Portal\Models\Supplier;
use App\Models\Portal;
use App\System\Repositories\PortalRepository;
use App\System\Repositories\SupplierRepository;
use App\Portal\Services\SupplierService as PortalSupplierService;

/**
 * Class SupplierService
 *
 * @package App\System\Services
 */
class SupplierService
{
    /** @var PortalRepository */
    private $portalRepository;
    /** @var SupplierRepository */
    private $supplierRepository;

    /**
     * SupplierService constructor.
     *
     * @param SupplierRepository      $supplierRepository
     * @param PortalSupplierService   $portalSupplierService
     * @param PortalConnectionService $portalConnectionService
     * @param PortalRepository        $portalRepository
     */
    public function __construct(
        SupplierRepository $supplierRepository,
        PortalRepository $portalRepository
    ) {
        $this->supplierRepository = $supplierRepository;
        $this->portalRepository = $portalRepository;
    }

    /**
     * @param array $data
     *
     * @return Supplier|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $supplier = $this->supplierRepository->create($data);

        if (!$supplier) {
            return false;
        }

        return $supplier->fresh();
    }

    /**
     * @param Supplier $supplier
     * @param array $data
     *
     * @return Supplier
     * @throws \Exception
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier->fresh();
    }

    /**
     * @param Supplier $supplier
     * @param int $portalId
     *
     * @return Supplier
     * @throws \Exception
     */
    public function duplicate(Supplier $supplier, $portalId)
    {
        $portal = Portal::query()->find($portalId);
        $exists = $supplier->portals()->where('portal_id', $portalId)->first();
        if (!$exists) {
            $supplier->portals()->save($portal, ['status_id' => Supplier::STATUS_ACTIVE]);
        }

        return $supplier;
    }
}
