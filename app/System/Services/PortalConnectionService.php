<?php

namespace App\System\Services;

use App\Models\Portal;
use App\System\Repositories\PortalRepository;

/**
 * Class PortalConnectionService
 *
 * @package App\System\Services
 */
class PortalConnectionService
{
    /** @var PortalRepository */
    private $portalRepository;

    /**
     * PortalConnectionService constructor.
     *
     * @param PortalRepository $portalRepository
     */
    public function __construct(PortalRepository $portalRepository)
    {
        $this->portalRepository = $portalRepository;
    }

    /**
     * Returns portal by id
     *
     * @param $id
     *
     * @return Portal|null
     */
    public function getPortal($portalId): ?Portal
    {
        return $this->portalRepository->find($portalId);
    }
}
