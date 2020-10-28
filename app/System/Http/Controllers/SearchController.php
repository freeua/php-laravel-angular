<?php

namespace App\System\Http\Controllers;

use App\Http\Requests\SearchLiveRequest;
use App\System\Http\Requests\SearchRequest;
use App\System\Http\Resources\SearchLiveResource;
use App\System\Http\Resources\SearchResource;
use App\System\Services\SearchService;

/**
 * Class SearchController
 *
 * @package App\System\Http\Controllers
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
