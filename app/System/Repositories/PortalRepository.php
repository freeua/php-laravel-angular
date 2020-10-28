<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\Models\Portal;
use Illuminate\Support\Facades\Cache;

/**
 * Class PortalRepository
 *
 * @package App\System\Repositories
 * @method Portal find(int $id, array $relations = [])
 */
class PortalRepository extends BaseRepository
{
    /** @var array */
    protected $searchWhereColumns = [
        'company_vat',
        'name',
        'domain'
    ];

    /**
     * PortalRepository constructor.
     *
     * @param Portal                 $portal
     */
    public function __construct(
        Portal $portal
    ) {
        $this->model = $portal;
    }

    /**
     * @param array $data
     *
     * @return Portal|false
     */
    public function create(array $data)
    {
        $portal = $this->model->newInstance();


        $portal->name = $data['name'];
        $portal->domain = $data['domain'];
        $portal->admin_first_name = $data['admin_first_name'];
        $portal->admin_last_name = $data['admin_last_name'];
        $portal->admin_email = $data['admin_email'];
        $portal->company_name = $data['company_name'];
        $portal->company_city_id = $data['company_city_id'];
        $portal->company_address = $data['company_address'];
        $portal->company_vat = $data['company_vat'];
        $portal->status()->associate($data['status_id']);

        return $portal->save() ? $portal : false;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return Portal|false
     */
    public function update(int $id, array $data)
    {
        $portal = $this->find($id);

        $portal->fill($data);

        return $portal->save() ? $portal : false;
    }

    public function findByDomain($domain)
    {
        $portal = Cache::remember("portals.$domain", 2400, function () use ($domain) {
            return $this->findBy('domain', $domain)->first();
        });
        return $portal;
    }

    public function findByAppKey($appKey)
    {
        $portal = Cache::remember("portals.$appKey", 2400, function () use ($appKey) {
            return $this->findBy('application_key', $appKey);
        });
        return $portal;
    }
}
