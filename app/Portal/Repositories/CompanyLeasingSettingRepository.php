<?php

namespace App\Portal\Repositories;

use App\Repositories\BaseRepository;
use App\Models\LeasingCondition;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class CompanyLeasingSettingRepository
 *
 * @package App\Portal\Repositories
 * @method LeasingCondition find(int $id, array $relations = [])
 */
class CompanyLeasingSettingRepository extends BaseRepository
{
    /**
     * CompanyLeasingSettingRepository constructor.
     *
     * @param LeasingCondition $companyLeasingSetting
     */
    public function __construct(LeasingCondition $companyLeasingSetting)
    {
        $this->model = $companyLeasingSetting;
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
        $model->company_id = $data['company_id'];
        $model->factor = $data['factor'];
        $model->period = $data['period'];
        $model->insurance = $data['insurance'];
        $model->service_rate = $data['service_rate'];
        $model->residual_value = $data['residual_value'];
        $model->active_at = Carbon::parse($data['active_at']);
        if ($data['inactive_at']) {
            $model->inactive_at = Carbon::parse($data['inactive_at']);
        }

        return $model->save() ? $model : false;
    }

    /**
     * @param int   $companyId
     * @param array $data
     *
     * @return void
     * @throws \Exception
     */
    public function updateAllByCompanyId(int $companyId, array $data): void
    {
        /** @var Collection $exitsLeasingSettingsIds */
        $exitsLeasingSettingsIds = $this->model
            ->where('company_id', $companyId)
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
                $leasingSetting['company_id'] = $companyId;
                $this->create($leasingSetting);
            }
        }
    }
}
