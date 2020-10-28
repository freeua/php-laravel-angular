<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\Models\LeasingCondition;
use Illuminate\Support\Collection;

/**
 * Class PortalLeasingSettingRepository
 *
 * @package App\System\Repositories
 * @method LeasingCondition find(int $id, array $relations = [])
 */
class PortalLeasingSettingRepository extends BaseRepository
{
    /**
     * PortalLeasingSettingsRepository constructor.
     *
     * @param LeasingCondition $portalLeasingSetting
     */
    public function __construct(LeasingCondition $portalLeasingSetting)
    {
        $this->model = $portalLeasingSetting;
    }

    /**
     * @param array $data
     *
     * @return LeasingCondition|false
     */
    public function create(array $data)
    {
        $model = $this->model->newInstance();

        $model->name = $data['name'];
        $model->product_category_id = $data['product_category_id'];
        $model->portal_id = $data['portal_id'];
        $model->period = $data['period'];
        $model->insurance = $data['insurance'];
        $model->factor = $data['factor'];
        $model->default = $data['default'];
        $model->service_rate = $data['service_rate'];
        $model->residual_value = $data['residual_value'];

        return $model->save() ? $model : false;
    }

    /**
     * @param int   $portalId
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    public function updateAllByPortalId(int $portalId, array $data): void
    {
        /** @var Collection $exitsLeasingSettingsIds */
        $exitsLeasingSettingsIds = $this->model
            ->where('portal_id', $portalId)
            ->get()
            ->pluck('id', 'id');

        foreach ($data as $leasingSetting) {
            if (!empty($leasingSetting['id'])) {
                $exitsLeasingSettingsIds->forget($leasingSetting['id']);
            }
        }

        if ($exitsLeasingSettingsIds->count()) {
            $this->butchDelete($exitsLeasingSettingsIds->toArray());
        }

        foreach ($data as $leasingSetting) {
            if (!empty($leasingSetting['id'])) {
                $leasingSettingId = $leasingSetting['id'];
                unset($leasingSetting['id']);
                $this->update($leasingSettingId, $leasingSetting);
            } else {
                $leasingSetting['portal_id'] = $portalId;
                $this->create($leasingSetting);
            }
        }
    }
}
