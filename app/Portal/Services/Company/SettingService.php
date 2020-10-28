<?php

namespace App\Portal\Services\Company;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Portal\Helpers\AuthHelper;
use App\Helpers\TextHelper;
use App\Models\Text;
use App\Portal\Repositories\Company\SettingRepository;
use App\Portal\Repositories\CompanyLeasingSettingRepository;
use App\Portal\Repositories\CompanyRepository;
use App\Repositories\TextRepository;
use Illuminate\Support\Collection;

/**
 * Class SettingService
 *
 * @package App\Portal\Services\Company
 */
class SettingService extends \App\Portal\Services\Base\SettingService
{
    /** @var CompanyLeasingSettingRepository */
    private $companyLeasingSettingRepository;
    /** @var CompanyRepository */
    private $companyRepository;
    /** @var SettingRepository */
    protected $settingsRepository;
    /** @var TextRepository */
    protected $textRepository;

    /**
     * UserService constructor.
     *
     * @param SettingRepository $settingsRepository
     * @param CompanyRepository $companyRepository
     * @param CompanyLeasingSettingRepository $companyLeasingSettingRepository
     */
    public function __construct(
        SettingRepository $settingsRepository,
        CompanyRepository $companyRepository,
        TextRepository $textRepository,
        CompanyLeasingSettingRepository $companyLeasingSettingRepository
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->companyRepository = $companyRepository;
        $this->textRepository = $textRepository;
        $this->companyLeasingSettingRepository = $companyLeasingSettingRepository;
    }

    /**
     * @param array $data
     *
     * @return Collection
     * @throws \Exception
     */
    public function update(array $data): array
    {

        $companyData = [
            'status_id' => $data['status_id'],
            'max_user_contracts' => $data['max_user_contracts'],
            'max_user_amount' => $data['max_user_amount'],
            'insurance_covered' => $data['insurance_covered'],
            'insurance_covered_type' => $data['insurance_covered_type'],
            'insurance_covered_amount' => $data['insurance_covered_amount'],
            'maintenance_covered' => $data['maintenance_covered'],
            'maintenance_covered_type' => $data['maintenance_covered_type'],
            'maintenance_covered_amount' => $data['maintenance_covered_amount'],
            'leasing_rate' => $data['leasing_rate'],
            'leasing_rate_type' => $data['leasing_rate_type'],
            'leasing_rate_amount' => $data['leasing_rate_amount'],
        ];

        $this->companyRepository->update(AuthHelper::companyId(), $companyData);

        return $this->all();
    }

    /**
     * @return Collection
     */
    public function all(): array
    {
        $data = [];
        $company = $this->companyRepository->find(AuthHelper::companyId());
        $data['logo'] = $company->logo;
        $data['color'] = $company->color;
        $data['id'] = $company->id;
        $data['company_name'] = $company->name;
        $data['is_accept_employee'] = $company->is_accept_employee;
        $data['s_pedelec_disable'] = $company->s_pedelec_disable;
        $data['gross_conversion'] = $company->gross_conversion;
        $data['pecuniary_advantage'] = $company->pecuniary_advantage;
        $data['include_insurance_rate'] = $company->include_insurance_rate;
        $data['include_service_rate'] = $company->include_service_rate;
        $data['boni_number'] = $company->boni_number;
        $data['gp_number'] = $company->gp_number;
        $data['texts'] = $this->getTextSettingsCollection();

        if (AuthHelper::isCompanyAdmin()) {
            $data['name'] = PortalHelper::name();
            $data['domain'] = PortalHelper::domain();
            $data['admin_first_name'] = $company->admin_first_name;
            $data['admin_last_name'] = $company->admin_last_name;
            $data['admin_email'] = $company->admin_email;
            $data['company_zip'] = $company->zip;
            $data['company_city_id'] = $company->city_id;
            $data['company_address'] = $company->address;
            $data['company_vat'] = $company->vat;
            $data['max_user_contracts'] = $company->max_user_contracts;
            $data['max_user_amount'] = $company->max_user_amount;
            $data['insurance_covered'] = $company->insurance_covered;
            $data['insurance_covered_type'] = $company->insurance_covered_type;
            $data['insurance_covered_amount'] = $company->insurance_covered_amount;
            $data['maintenance_covered'] = $company->maintenance_covered;
            $data['maintenance_covered_type'] = $company->maintenance_covered_type;
            $data['maintenance_covered_amount'] = $company->maintenance_covered_amount;
            $data['leasing_budget'] = $company->leasing_budget;
            $data['leasing_rate'] = $company->leasing_rate;
            $data['leasing_rate_type'] = $company->leasing_rate_type;
            $data['leasing_rate_amount'] = $company->leasing_rate_amount;
            $data['status'] = $company->status;
            $data['leasing_settings'] = $company->activeLeasingSettings;
            $data['uses_default_subsidies'] = $company->uses_default_subsidies;
        }
        if (AuthHelper::isEmployee()) {
            $data['is_accept_offer'] = AuthHelper::user()->is_accept_offer;
        }

        return $data;
    }

    private function getTextSettingsCollection()
    {
        $textCollection = \Cache::rememberForever(Text::getCacheCollectionKey(), function () {
            return TextHelper::getSettingsCollection($this->textRepository->all());
        });
        return $textCollection;
    }

    /**
     * @param Company $company
     *
     * @return void
     */
    public function addDefaultSettings(Company $company): void
    {
        $settings = [
            'logo' => '',
            'color' => '#efab06'
        ];

        foreach ($settings as $key => $value) {
            $this->settingsRepository->create($company->id, $key, $value);
        }
    }
}
