<?php

namespace App\Portal\Repositories;

use App\Repositories\BaseRepository;
use App\Portal\Models\Setting;

/**
 * Class SettingRepository
 *
 * @package App\Portal\Repositories
 * @method Setting find(int $id, array $relations = [])
 */
class SettingRepository extends BaseRepository
{
    /**
     * SettingsRepository constructor.
     *
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->model = $setting;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return bool|Setting
     */
    public function create(string $key, string $value)
    {
        $setting = $this->model->newInstance();
        $setting->key = $key;
        $setting->value = $value;

        return $setting->save() ? $setting : false;
    }
}
