<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use App\System\Models\Setting;
use Illuminate\Support\Facades\Auth;

/**
 * Class SettingRepository
 *
 * @package App\System\Repositories
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
     * @param int    $userId
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

    /**
     * @param int $userId
     *
     * @return Collection|static[]
     */
    public function userAll(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
