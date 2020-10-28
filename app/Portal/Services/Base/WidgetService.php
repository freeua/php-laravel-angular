<?php

namespace App\Portal\Services\Base;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Widget;
use App\Services\BaseWidgetService;
use App\Portal\Repositories\WidgetRepository;
use App\System\Repositories\ContractRepository;
use App\Repositories\OrderRepository;

/**
 * Class WidgetService
 *
 * @package App\Portal\Services\Base
 */
abstract class WidgetService extends BaseWidgetService
{
    /** @var OrderRepository */
    protected $orderRepository;
    /** @var ContractRepository */
    protected $contractRepository;
    /** @var WidgetRepository */
    protected $widgetRepository;

    /**
     * WidgetService constructor.
     *
     * @param WidgetRepository    $widgetRepository
     * @param ContractRepository  $contractRepository
     * @param OrderRepository     $orderRepository
     */
    public function __construct(
        WidgetRepository $widgetRepository,
        ContractRepository $contractRepository,
        OrderRepository $orderRepository
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->contractRepository = $contractRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $data
     *
     * @return Widget
     */
    public function create(array $data): Widget
    {
        if (!isset($data['user_id'])) {
            $data['user_id'] = AuthHelper::id();
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
     * @param $params
     *
     * @return array
     */
    protected function getContractsCreatedData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->contractRepository->getHistoryCreated(PortalHelper::id(), $params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->contractRepository->getHistoryCreated(PortalHelper::id(), $params, true), $params, true);

        return $result;
    }

    /**
     * @param $params
     *
     * @return array
     */
    protected function getOrdersData(array $params): array
    {
        $result['table'] = $this->formatDateWidgetData($this->orderRepository->getHistoryCount(PortalHelper::id(), $params), $params);
        $result['chart'] = $this->formatDateWidgetData($this->orderRepository->getHistoryCount(PortalHelper::id(), $params, true), $params, true);

        return $result;
    }

    /**
     * @param array    $ids
     * @param int|null $userId
     *
     * @return bool
     */
    public function validateUser(array $ids, ?int $userId = null): bool
    {
        $userId = $userId ?: AuthHelper::id();

        return !$this->widgetRepository->newQuery()
            ->where('user_id', '!=', $userId)
            ->whereIn('id', $ids)
            ->exists();
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
     * @param int    $userId
     * @param string $role
     *
     * @return void
     */
    public function addDefaultUserWidgets(int $userId, string $role): void
    {
        $sources = Widget::getDefaultWidgetSources($role);

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
