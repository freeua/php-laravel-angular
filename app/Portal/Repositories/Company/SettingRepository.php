<?php

namespace App\Portal\Repositories\Company;

use App\Portal\Helpers\AuthHelper;
use App\Repositories\BaseRepository;
use App\Portal\Models\CompanySetting;
use Illuminate\Support\Collection;

/**
 * Class SettingRepository
 *
 * @package App\Portal\Repositories\Company
 * @method CompanySetting find(int $id, array $relations = [])
 */
class SettingRepository extends BaseRepository
{
    /**
     * SettingsRepository constructor.
     *
     * @param CompanySetting $setting
     */
    public function __construct(CompanySetting $setting)
    {
        $this->model = $setting;
    }

    /**
     * @param int    $company_id
     * @param string $key
     * @param string $value
     *
     * @return bool|CompanySetting
     */
    public function create(int $company_id, string $key, string $value)
    {
        $setting = $this->model->newInstance();
        $setting->company_id = $company_id;
        $setting->key = $key;
        $setting->value = $value;

        return $setting->save() ? $setting : false;
    }

    /**
     * @param int $companyId
     *
     * @return Collection|static[]
     */
    public function companyAll(int $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    /**
     * @param bool $pluck
     *
     * @return Collection|static[]
     */
    public function currentCompanyAll(bool $pluck = true): Collection
    {
        $result = $this->companyAll(AuthHelper::companyId());

        return $pluck ? $result->pluck('value', 'key') : $result;
    }
}
