<?php

namespace App\Portal\Repositories\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\SupplierSetting;
use Illuminate\Support\Collection;

/**
 * Class SettingRepository
 *
 * @package App\Portal\Repositories\Supplier
 * @method SupplierSetting find(int $id, array $relations = [])
 */
class SettingRepository extends BaseRepository
{
    /**
     * SettingsRepository constructor.
     *
     * @param SupplierSetting $setting
     */
    public function __construct(SupplierSetting $setting)
    {
        $this->model = $setting;
    }

    /**
     * @param int    $supplierId
     * @param string $key
     * @param string $value
     *
     * @return bool|SupplierSetting
     */
    public function create(int $supplierId, string $key, string $value)
    {
        $setting = $this->model->newInstance();
        $setting->supplier_id = $supplierId;
        $setting->key = $key;
        $setting->value = $value;

        return $setting->save() ? $setting : false;
    }

    /**
     * @param int $supplierId
     *
     * @return Collection|static[]
     */
    public function supplierAll(int $supplierId): Collection
    {
        return $this->model->where('supplier_id', $supplierId)->get();
    }

    /**
     * @return Collection|static[]
     */
    public function currentSupplierAll(): Collection
    {
        return $this->supplierAll(AuthHelper::supplierId());
    }
}
