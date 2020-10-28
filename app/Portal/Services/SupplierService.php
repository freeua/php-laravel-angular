<?php

namespace App\Portal\Services;

use App\Helpers\PortalHelper;
use App\Models\City;
use App\Models\Companies\Company;
use App\Portal\Models\Supplier;
use App\Portal\Notifications\Supplier\SupplierCreated;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Repositories\ProductBrandRepository;
use App\Portal\Repositories\ProductCategoryRepository;
use App\Portal\Repositories\ProductModelRepository;
use App\Portal\Repositories\SupplierRepository;
use App\Modules\TechnicalServices\Repositories\TechnicalServicesRepository;
use App\Repositories\OrderRepository;
use App\Portal\Services\Supplier\SettingService;
use App\System\Repositories\PortalRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Class SupplierService
 *
 * @package App\Portal\Services
 */
class SupplierService
{
    /** @var ProductCategoryRepository */
    private $productCategoryRepository;
    /** @var ProductModelRepository */
    private $productModelRepository;
    /** @var ProductBrandRepository */
    private $productBrandRepository;
    /** @var SettingService */
    private $settingService;
    /** @var UserService */
    private $userService;
    /** @var CompanyRepository */
    private $companyRepository;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var TechnicalServicesRepository */
    private $technicalServiceRepository;
    /** @var SupplierRepository */
    private $supplierRepository;
    /**
     * @var PortalRepository
     */
    private $portalRepository;

    /**
     * SupplierService constructor.
     *
     * @param SupplierRepository        $supplierRepository
     * @param SupplierRepository  $systemSupplierRepository
     * @param OrderRepository           $orderRepository
     * @param CompanyRepository         $companyRepository
     * @param UserService               $userService
     * @param SettingService            $settingService
     * @param ProductBrandRepository    $productBrandRepository
     * @param ProductModelRepository    $productModelRepository
     * @param ProductCategoryRepository $productCategoryRepository
     * @param PortalRepository          $portalRepository
     */
    public function __construct(
        SupplierRepository $supplierRepository,
        OrderRepository $orderRepository,
        CompanyRepository $companyRepository,
        UserService $userService,
        SettingService $settingService,
        ProductBrandRepository $productBrandRepository,
        ProductModelRepository $productModelRepository,
        ProductCategoryRepository $productCategoryRepository,
        PortalRepository $portalRepository
    ) {
        $this->supplierRepository = $supplierRepository;
        $this->orderRepository = $orderRepository;
        $this->companyRepository = $companyRepository;
        $this->userService = $userService;
        $this->settingService = $settingService;
        $this->productBrandRepository = $productBrandRepository;
        $this->productModelRepository = $productModelRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->portalRepository = $portalRepository;
    }

    /**
     * @param array $data
     *
     * @return Supplier|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $portal = PortalHelper::getPortal();
        $supplier = $this->supplierRepository->create($data, $portal);

        if (!$supplier) {
            return false;
        }
        $city = City::where('id', $data['city_id'])->first();
        $data['city'] = $city->toArray();
        $data = $this->addUserData($data);

        $this->settingService->addDefaultSettings($supplier);

        $data['user']['supplier_id'] = $supplier->id;

        $admin = $this->userService->createSupplierAdmin($data['user'], $portal);

        Notification::send($admin, (new SupplierCreated()));

        return $supplier->fresh();
    }

    /**
     * @param Supplier $supplier
     * @param array    $data
     *
     * @return Supplier|false
     */
    public function selfUpdate(Supplier $supplier, array $data)
    {
        if (isset($data['status_id'])) {
            $statusId = $data['status_id'];
            unset($data['status_id']);
            $supplier->portals()->updateExistingPivot(PortalHelper::id(), ['status_id' => $statusId]);
        }
        $supplier->update($data);
        if (isset($data['blind_discount'])) {
            $supplier->portals()->updateExistingPivot(PortalHelper::id(), ['blind_discount' => $data['blind_discount']]);
        }
        $supplier->refresh();

        return $supplier;
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return Supplier|false
     */
    public function selfUpdateById(int $id, array $data)
    {
        $supplier = $this->supplierRepository->find($id);

        if (!$supplier) {
            return false;
        }

        return $this->selfUpdate($supplier, $data);
    }


    /**
     * @param int $supplierId
     * @param string $status
     *
     * @return Collection
     */
    public function getOrdersPerCompany(int $supplierId, ?string $status): Collection
    {
        return $this->orderRepository->getPerSupplierCompanyCount($supplierId, Supplier::WIDGET_ITEMS_COUNT, $status);
    }

    /**
     * @param int    $id
     * @param string $status
     *
     * @return Collection
     */
    public function getOffersPerCompany(int $id, ?string $status): Collection
    {
        return $this->companyRepository->getSupplierOffersPerCompanyCount($id, Supplier::WIDGET_ITEMS_COUNT, $status);
    }
    /**
     * @param int    $id
     * @param string $status
     *
     * @return Collection
     */
    public function getTechnicalServicesPerCompany(int $supplierId, ?string $status): Collection
    {
        return $this->technicalServiceRepository->getPerSupplierCompanyCount($supplierId, Supplier::WIDGET_ITEMS_COUNT, $status);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    private function addUserData(array $data)
    {
        $data['user'] = [
            'first_name'  => $data['admin_first_name'],
            'last_name'   => $data['admin_last_name'],
            'email'       => $data['admin_email'],
            'phone'       => $data['phone'],
            'postal_code' => $data['zip'],
            'city_name'   => $data['city']['name'],
            'street'      => $data['address'],
        ];

        return $data;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function import(array $data)
    {
        $suppliersIds = [];
        foreach ($data['suppliers'] as $item) {
            $item['city_id'] = City::where('name', $item['city']['name'])->pluck('id')->first();
            $supplier = $this->create($item);
            if ($supplier) {
                $suppliersIds[] = $supplier->id;
            }
        }

        if (isset($data['companies']) && !empty($data['companies'])) {
            foreach ($data['companies'] as $item) {
                $company = Company::where('id', $item['id'])->first();
                $company->suppliers()->attach($suppliersIds);
            }
        }
    }
}
