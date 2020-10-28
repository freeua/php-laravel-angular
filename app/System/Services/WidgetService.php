<?php

namespace App\System\Services;

use App\Models\Portal;
use App\Portal\Repositories\CompanyRepository;
use App\Services\BaseWidgetService;
use App\System\Models\Widget;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use App\Portal\Repositories\SupplierRepository;
use App\System\Repositories\WidgetRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class WidgetService
 *
 * @package App\System\Services
 */
class WidgetService extends BaseWidgetService
{
    /** @var CompanyRepository */
    private $companyRepository;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var SupplierRepository */
    private $supplierRepository;
    /** @var ContractRepository */
    private $contractRepository;
    /** @var WidgetRepository */
    private $widgetRepository;

    public function __construct(
        WidgetRepository $widgetRepository,
        ContractRepository $contractRepository,
        SupplierRepository $supplierRepository,
        OrderRepository $orderRepository,
        CompanyRepository $companyRepository
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->contractRepository = $contractRepository;
        $this->supplierRepository = $supplierRepository;
        $this->orderRepository = $orderRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param array $data
     *
     * @return Widget
     */
    public function create(array $data): Widget
    {
        if (!isset($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }

        return $this->widgetRepository->create($data);
    }

    /**
     * @param Widget $widget
     * @param array  $data
     *
     * @return Widget
     */
    public function update(Widget $widget, array $data): Widget
    {
        $this->widgetRepository->update($widget->id, $data);

        return $widget->fresh();
    }

    /**
     * @param Widget $widget
     * @param array  $params
     *
     * @return Widget
     */
    public function loadData(Widget $widget, array $params): Widget
    {
        $params['date_from'] = Carbon::createFromTimestampUTC($params['date_from'])->toDateString();
        $params['date_to'] = Carbon::createFromTimestampUTC($params['date_to'])->toDateString();

        switch ($widget->source) {
            case Widget::SOURCE_CONTRACTS_CREATED:
                $widget->data = $this->getContractsCreatedData($params);
                break;
            case Widget::SOURCE_NO_OF_SUPPLIERS:
                $widget->data = $this->getSuppliersData($params);
                break;
            case Widget::SOURCE_NO_OF_ORDERS:
                $widget->data = $this->getOrdersData($params);
                break;
            case Widget::SOURCE_PRODUCTS_PER_SUPPLIER:
                $widget->data = $this->getProductsPerSupplierData($params);
                break;
            case Widget::SOURCE_NO_OF_SUPPLIERS_COMPANY:
                $widget->data = $this->getSuppliersCompanyData($params);
                break;
            case Widget::SOURCE_COMPANY_AND_EMPLOYEES:
                $widget->data = $this->getCompanyEmployeesData($params);
                break;
        }

        return $widget;
    }

    /**
     * @param array $params
     *
     * @return Collection
     */
    public function getAllWithData(array $params): Collection
    {
        $widgets = $this->widgetRepository->currentUserAll();

        /** @var Widget $widget */
        foreach ($widgets as $widget) {
            $this->loadData($widget, $params);
        }

        return $widgets;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getContractsCreatedData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->contractRepository
                ->getHistoryCreated($params['portal_id'], $params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->contractRepository
            ->getHistoryCreated($params['portal_id'], $params, true), $params, true);

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getSuppliersData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->supplierRepository->getHistoryCreated($params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->supplierRepository
            ->getHistoryCreated($params, true), $params, true);

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getProductsPerSupplierData(array $params): array
    {
        $all = $this->supplierRepository->getHistoryProducts($params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getSuppliersCompanyData(array $params): array
    {
        $all = $this->supplierRepository->getHistoryAssignedToCompany($params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }


    /**
     * @param $params
     *
     * @return array
     */
    public function getOrdersData(array $params): array
    {
        $all = $this->orderRepository->getPerCompanyHistoryCount($params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getCompanyEmployeesData(array $params): array
    {
        $all = $this->companyRepository->getHistoryEmployees($params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }

    /**
     * @param array $positions
     *
     * @return bool
     */
    public function updatePositions(array $positions): bool
    {
        $result = false;

        foreach ($positions as $id => $position) {
            $result = $this->widgetRepository->update($id, ['position' => $position]);
        }

        return $result;
    }

    /**
     * @param int $userId
     *
     * @return void
     */
    public function addDefaultUserWidgets(int $userId): void
    {
        $sources = Widget::getDefaultWidgetSources();

        $position = 1;

        foreach ($sources as $source) {
            $this->widgetRepository->create([
                'user_id'  => $userId,
                'source'   => $source,
                'style'    => Widget::STYLE_LINE,
                'position' => $position++,
            ]);
        }
    }
}
