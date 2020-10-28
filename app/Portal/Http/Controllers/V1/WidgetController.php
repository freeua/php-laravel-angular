<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Http\Requests\V1\ListWidgetRequest;
use App\Portal\Http\Requests\V1\ViewWidgetRequest;
use App\Portal\Http\Resources\V1\WidgetResource;
use App\Portal\Models\Widget;
use App\Portal\Repositories\WidgetRepository;
use App\Portal\Services\WidgetService;

/**
 * Class WidgetController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class WidgetController extends Base\WidgetController
{
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
}
