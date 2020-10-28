<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\SearchLiveRequest;
use App\Portal\Http\Requests\V1\Employee\SearchRequest;
use App\Portal\Http\Resources\V1\Employee\SearchLiveResource;
use App\Portal\Http\Resources\V1\Employee\SearchResource;
use App\Portal\Services\Employee\SearchService;

/**
 * Class SearchController
 *
 * @package App\Portal\Http\Controllers\V1\Employee
 */
class SearchController extends Controller
{
    /** @var SearchService */
    private $searchService;

    /**
     * UserController constructor.
     *
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        parent::__construct();

        $this->searchService = $searchService;
    }

    /**
     * Returns search result
     *
     * @param SearchRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $result = $this->searchService->search($request->validated());

        return response()->success(new SearchLiveResource($result));
    }

    /**
     * Return live search result
     *
     * @param SearchLiveRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function live(SearchLiveRequest $request)
    {
        $result = $this->searchService->live($request->validated());

        return response()->success(new SearchLiveResource($result));
    }
}
