<?php

namespace App\Portal\Services\Company;

use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Repositories\Company\OfferRepository;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Models\Widget;
use App\Portal\Models\Contract;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use App\Portal\Repositories\WidgetRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class WidgetService
 *
 * @package App\Portal\Services\Company
 */
class WidgetService extends \App\Portal\Services\Base\WidgetService
{
    /** @var CompanyRepository */
    protected $companyRepository;
    /** @var OrderRepository */
    protected $orderRepository;
    /** @var OfferRepository */
    protected $offerRepository;
    /** @var ContractRepository */
    protected $contractRepository;
    /** @var WidgetRepository */
    protected $widgetRepository;

    /**
     * WidgetService constructor.
     *
     * @param WidgetRepository    $widgetRepository
     * @param ContractRepository  $contractRepository
     * @param OfferRepository     $offerRepository
     * @param OrderRepository     $orderRepository
     * @param CompanyRepository   $companyRepository
     */
    public function __construct(
        WidgetRepository $widgetRepository,
        ContractRepository $contractRepository,
        OfferRepository $offerRepository,
        OrderRepository $orderRepository,
        CompanyRepository $companyRepository
    ) {
        parent::__construct($widgetRepository, $contractRepository, $orderRepository);

        $this->offerRepository = $offerRepository;
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
                $params['company_id'] = AuthHelper::companyId();
                $widget->data = $this->getContractsCreatedData($params);
                break;
            case Widget::SOURCE_NO_OF_ORDERS:
                $params['company_id'] = AuthHelper::companyId();
                $widget->data = $this->getOrdersData($params);
                break;
            case Widget::SOURCE_OFFERS_REJECTED:
                $params['status_id'] = [Offer::STATUS_REJECTED];
                $widget->data = $this->getOffersData($params);
                break;
            case Widget::SOURCE_OFFERS_ACCEPTED:
                $params['status_id'] = [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED];
                $widget->data = $this->getOffersData($params);
                break;
            case Widget::SOURCE_NO_OF_ORDERS_AND_STATUS:
                $widget->data = $this->getOrdersStatusesData($params);
                break;
            case Widget::SOURCE_NO_OF_EMPLOYEE_CONTRACTS:
                $params['status_id'] = [Contract::STATUS_ACTIVE];
                $widget->data = $this->getEmployeeContracts($params);
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
    private function getOffersData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->offerRepository->getHistoryCount($params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->offerRepository->getHistoryCount($params, true), $params, true);

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getOrdersStatusesData(array $params): array
    {
        $data = $this->orderRepository->getCompanyStatusCount(PortalHelper::id(), AuthHelper::companyId(), $params);

        $result['table'] = $data;
        $result['chart'] = $data;

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function getEmployeeContracts(array $params): array
    {
        $all = $this->contractRepository->getCompanyPerEmployeeHistoryCount(PortalHelper::id(), AuthHelper::companyId(), $params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }
}
