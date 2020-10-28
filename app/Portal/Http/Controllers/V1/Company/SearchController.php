<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Portal\Http\Controllers\Controller;
use App\Http\Requests\SearchLiveRequest;
use App\Portal\Http\Requests\V1\Company\SearchRequest;
use App\Portal\Http\Resources\V1\Company\SearchLiveResource;
use App\Portal\Http\Resources\V1\Company\SearchResource;
use App\Portal\Services\Company\SearchService;

/**
 * Class SearchController
 *
 * @package App\Portal\Http\Controllers\V1\Company
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
