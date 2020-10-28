<?php

namespace App\System\Http\Controllers;

use App\System\Http\Requests\CreateWidgetRequest;
use App\System\Http\Requests\UpdateWidgetRequest;
use App\System\Http\Requests\UpdateWidgetsPositionRequest;
use App\System\Http\Requests\ListWidgetRequest;
use App\System\Http\Requests\ViewWidgetRequest;
use App\System\Http\Resources\WidgetResource;
use App\System\Models\Widget;
use App\System\Repositories\WidgetRepository;
use App\System\Services\WidgetService;

/**
 * Class WidgetController
 *
 * @package App\System\Http\Controllers
 */
class WidgetController extends Controller
{
    /** @var WidgetRepository */
    private $widgetRepository;
    /** @var WidgetService */
    private $widgetService;

    /**
     * Create a new controller instance.
     *
     * @param WidgetService    $widgetService
     * @param WidgetRepository $widgetRepository
     */
    public function __construct(WidgetService $widgetService, WidgetRepository $widgetRepository)
    {
        parent::__construct();

        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
    }

    /**
     * Get list of widgets
     *
     * @param ListWidgetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListWidgetRequest $request)
    {
        return response()->success(
            WidgetResource::collection($this->widgetService->getAllWithData($request->validated()))
        );
    }

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
     * View widget with data
     *
     * @param Widget            $widget
     * @param ViewWidgetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Widget $widget, ViewWidgetRequest $request)
    {
        $this->widgetService->loadData($widget, $request->validated());

        return response()->success(new WidgetResource($widget));
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
