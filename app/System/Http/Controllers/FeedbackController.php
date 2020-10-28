<?php

namespace App\System\Http\Controllers;

use App\System\Http\Requests\CreateFeedbackRequest;
use App\System\Http\Resources\FeedbackCategoryResource;
use App\System\Repositories\FeedbackCategoryRepository;
use App\System\Services\FeedbackService;

/**
 * Class FeedbackController
 *
 * @package App\System\Http\Controllers
 */
class FeedbackController extends Controller
{
    /** @var FeedbackService */
    private $feedbackService;
    /** @var FeedbackCategoryRepository */
    private $feedbackCategoryRepository;

    /**
     * FeedbackController constructor.
     *
     * @param FeedbackService $feedbackService
     * @param FeedbackCategoryRepository $feedbackCategoryRepository
     */
    public function __construct(
        FeedbackService $feedbackService,
        FeedbackCategoryRepository $feedbackCategoryRepository
    ) {
        parent::__construct();

        $this->feedbackService = $feedbackService;
        $this->feedbackCategoryRepository = $feedbackCategoryRepository;
    }

    /**
     * @param CreateFeedbackRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateFeedbackRequest $request)
    {
        $this->feedbackService->create($request->validated());

        return response()->success();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        $categories = $this->feedbackCategoryRepository->all();

        return response()->success(FeedbackCategoryResource::collection($categories));
    }
}
