<?php

namespace App\Portal\Services\Supplier;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Offer;
use App\Portal\Repositories\Supplier\OfferRepository;
use App\Portal\Models\Widget;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;
use App\Portal\Repositories\WidgetRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class WidgetService
 *
 * @package App\Portal\Services\Supplier
 */
class WidgetService extends \App\Portal\Services\Base\WidgetService
{
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
     */
    public function __construct(
        WidgetRepository $widgetRepository,
        ContractRepository $contractRepository,
        OfferRepository $offerRepository,
        OrderRepository $orderRepository
    ) {
        parent::__construct($widgetRepository, $contractRepository, $orderRepository);

        $this->offerRepository = $offerRepository;
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
            case Widget::SOURCE_ORDERS_PER_COMPANY:
                $params['supplier_id'] = AuthHelper::supplierId();
                $widget->data = $this->getOrdersPerCompanyData($params);
                break;
            case Widget::SOURCE_OFFERS_REJECTED:
                $params['supplier_id'] = AuthHelper::supplierId();
                $params['status_id'] = [Offer::STATUS_REJECTED];
                $widget->data = $this->getOffersData($params);
                break;
            case Widget::SOURCE_OFFERS_ACCEPTED:
                $params['supplier_id'] = AuthHelper::supplierId();
                $params['status_id'] = [Offer::STATUS_ACCEPTED, Offer::STATUS_CONTRACT_APPROVED];
                $widget->data = $this->getOffersData($params);
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
    private function getOrdersPerCompanyData(array $params): array
    {
        $all = $this->orderRepository->getPerCompanyHistoryCount($params);

        $result['chart'] = $all->take(Widget::CHART_ITEMS_COUNT);
        $result['table'] = $all;

        return $result;
    }

    /**
     *
     * @param array $params
     *
     * @return array
     */
    private function getOffersData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->offerRepository->getHistoryCount($params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->offerRepository->getHistoryCount($params, true), $params, true);

        return $result;
    }
}
