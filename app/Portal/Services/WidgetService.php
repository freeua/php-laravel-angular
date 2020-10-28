<?php

namespace App\Portal\Services;

use App\Portal\Repositories\CompanyRepository;
use App\Portal\Models\Widget;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use App\Portal\Repositories\SupplierRepository;
use App\Portal\Repositories\WidgetRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class WidgetService
 *
 * @package App\Portal\Services
 */
class WidgetService extends Base\WidgetService
{
    /** @var CompanyRepository */
    protected $companyRepository;
    /** @var OrderRepository */
    protected $orderRepository;
    /** @var SupplierRepository */
    protected $supplierRepository;
    /** @var ContractRepository */
    protected $contractRepository;
    /** @var WidgetRepository */
    protected $widgetRepository;

    /**
     * WidgetService constructor.
     *
     * @param WidgetRepository         $widgetRepository
     * @param ContractRepository       $contractRepository
     * @param SupplierRepository       $supplierRepository
     * @param OrderRepository          $orderRepository
     * @param CompanyRepository        $companyRepository
     */
    public function __construct(
        WidgetRepository $widgetRepository,
        ContractRepository $contractRepository,
        SupplierRepository $supplierRepository,
        OrderRepository $orderRepository,
        CompanyRepository $companyRepository
    ) {
        parent::__construct($widgetRepository, $contractRepository, $orderRepository);

        $this->supplierRepository = $supplierRepository;
        $this->companyRepository = $companyRepository;
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
            case Widget::SOURCE_NO_OF_ORDERS:
                $widget->data = $this->getOrdersData($params);
                break;
            case Widget::SOURCE_ORDERS_PER_COMPANY:
                $widget->data = $this->getOrdersPerCompanyData($params);
                break;
            case Widget::SOURCE_NO_OF_EMPLOYEES:
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
        $widgets = $this->widgetRepository->currentUserAll(!empty($params['company_id']));

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
    private function getOrdersPerCompanyData(array $params): array
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
        if (!empty($params['company_id'])) {
            $result['table'] = $this->formatDateWidgetData(
                $this->companyRepository->getHistoryCompanyEmployees($params['company_id'], $params),
                $params
            );
            $result['chart'] = $this->formatDateWidgetData(
                $this->companyRepository->getHistoryCompanyEmployees($params['company_id'], $params, true),
                $params,
                true
            );
        } else {
            $all = $this->companyRepository->getHistoryEmployees($params);

            $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
            $result['table'] = $all;
        }

        return $result;
    }
}
