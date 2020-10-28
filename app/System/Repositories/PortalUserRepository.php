<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\Portal\Models\User as PortalUser;

/**
 * Class PortalUserRepository
 *
 * @package App\System\Repositories
 * @method PortalUser find(int $id, array $relations = [])
 */
class PortalUserRepository extends BaseRepository
{
    /**
     * PortalUserRepository constructor.
     *
     * @param PortalUser                 $portalUser
     */
    public function __construct(
        PortalUser $portalUser
    ) {
        $this->model = $portalUser;
    }

    /**
     * @param array $data
     *
     * @return PortalUser|false
     */
    public function create(array $data)
    {
        $portalUser = $this->model->newInstance();

        $portalUser->code = $data['code'];
        $portalUser->first_name = $data['first_name'];
        $portalUser->last_name = $data['last_name'];
        $portalUser->email = $data['email'];
        $portalUser->portal_id = $data['portal_id'];
        $portalUser->status_id = $data['status_id'] ?? PortalUser::STATUS_ACTIVE;

        return $portalUser->save() ? $portalUser->fresh() : false;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return PortalUser|false
     */
    public function update(int $id, array $data)
    {
        $portalUser = $this->find($id);

        $portalUser->fill($data);

        return $portalUser->save() ? $portalUser : false;
    }
}
