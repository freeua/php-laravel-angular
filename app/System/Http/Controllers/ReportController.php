<?php

namespace App\System\Http\Controllers;

use App\System\Http\Requests\CreateReportRequest;
use App\System\Http\Resources\Collections\ReportCategoryCollection;
use App\System\Repositories\ReportCategoryRepository;
use App\System\Services\ReportService;

/**
 * Class ReportController
 *
 * @package App\System\Http\Controllers
 */
class ReportController extends Controller
{
    /** @var ReportService */
    private $reportService;
    /** @var ReportCategoryRepository */
    private $reportCategoryRepository;

    /**
     * ReportController constructor.
     *
     * @param ReportService $reportService
     * @param ReportCategoryRepository $reportCategoryRepository
     */
    public function __construct(
        ReportService $reportService,
        ReportCategoryRepository $reportCategoryRepository
    ) {
        parent::__construct();

        $this->reportService = $reportService;
        $this->reportCategoryRepository = $reportCategoryRepository;
    }

    /**
     * @param CreateReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateReportRequest $request)
    {
        $this->reportService->create($request->validated());

        return response()->success();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        $categories = $this->reportCategoryRepository->all();

        return response()->success(new ReportCategoryCollection($categories));
    }
}
