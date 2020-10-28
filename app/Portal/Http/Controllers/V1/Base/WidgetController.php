<?php

namespace App\Portal\Http\Controllers\V1\Base;

use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\CreateWidgetRequest;
use App\Portal\Http\Requests\V1\UpdateWidgetRequest;
use App\Portal\Http\Requests\V1\UpdateWidgetsPositionRequest;
use App\Portal\Http\Resources\V1\WidgetResource;
use App\Portal\Models\Widget;
use App\Portal\Repositories\WidgetRepository;
use App\Portal\Services\WidgetService;

/**
 * Class WidgetController
 *
 * @package App\Portal\Http\Controllers\V1\Base
 */
abstract class WidgetController extends Controller
{
    /** @var WidgetRepository */
    protected $widgetRepository;
    /** @var WidgetService */
    protected $widgetService;

    /**
     * Create widget
     *
     * @param CreateWidgetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateWidgetRequest $request)
    {
        if (!$this->widgetRepository->checkUserLimit()) {
            return response()->error([__('widget.create.limit')], __('widget.create.limit'));
        }

        $widget = $this->widgetService->create($request->validated());

        return $widget
            ? response()->success((new WidgetResource($widget))->withoutData())
            : response()->error([__('widget.create.failed')], __('widget.create.failed'));
    }

    /**
     * Update widget
     *
     * @param Widget              $widget
     * @param UpdateWidgetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Widget $widget, UpdateWidgetRequest $request)
    {
        $widget = $this->widgetService->update($widget, $request->validated());

        return $widget
            ? response()->success((new WidgetResource($widget))->withoutData())
            : response()->error([__('widget.update.failed')], __('widget.update.failed'));
    }

    /**
     * Update widgets positions
     *
     * @param UpdateWidgetsPositionRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePositions(UpdateWidgetsPositionRequest $request)
    {
        $result = $this->widgetService->updatePositions($request->get('positions'));

        return $result
            ? response()->success()
            : response()->error([__('widget.update_position.failed')], __('widget.update_position.failed'));
    }

    /**
     * Delete widget
     *
     * @param Widget $widget
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Widget $widget)
    {
        $result = $this->widgetRepository->deleteModel($widget);

        return $result
            ? response()->success()
            : response()->error([__('widget.delete.failed')], __('widget.delete.failed'));
    }

    /**
     * Returns list of widget sources with titles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sources()
    {
        return response()->success(Widget::getSourceTitles());
    }
}
