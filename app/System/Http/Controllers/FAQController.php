<?php

namespace App\System\Http\Controllers;

use App\Http\Requests\DefaultListRequest;
use App\Portal\Models\Faq;
use App\Portal\Models\FaqCategory;
use App\System\Http\Requests\CreateFaqCategoryRequest;
use App\System\Http\Requests\CreateFAQRequest;
use App\System\Http\Requests\UpdateFaqCategoryRequest;
use App\System\Http\Requests\UpdateFAQRequest;
use App\System\Http\Resources\FaqCategoryResource;
use App\System\Http\Resources\FAQResource;
use App\System\Http\Resources\ListCollections\FaqCategoryListCollection;
use App\System\Http\Resources\ListCollections\FAQListCollection;
use App\System\Repositories\FaqCategoryRepository;
use App\System\Repositories\FAQRepository;
use App\System\Services\FAQService;
use Illuminate\Http\Request;

/**
 * Class FileController
 *
 * @package App\System\Http\Controllers
 */
class FAQController extends Controller
{
    /** @var FAQService */
    private $faqService;
    /** @var FAQRepository */
    private $faqRepository;
    /** @var FaqCategoryRepository */
    private $faqCategoryRepository;

    public function __construct(
        FAQService $faqService,
        FAQRepository $faqRepository,
        FaqCategoryRepository $faqCategoryRepository
    ) {
        $this->faqService = $faqService;
        $this->faqRepository = $faqRepository;
        $this->faqCategoryRepository = $faqCategoryRepository;
    }

    /**
     * @param DefaultListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DefaultListRequest $request)
    {
        $faqs = $this->faqRepository->list($request->validated());

        return response()->success(new FAQListCollection($faqs));
    }

    /**
     * @param CreateFAQRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateFAQRequest $request)
    {
        $faq = $this->faqService->create($request->validated());

        return $faq
            ? response()->success(new FAQResource($faq))
            : response()->error([__('faq.create.failed')], __('faq.create.failed'));
    }

    /**
     * Update an user
     *
     * @param FAQ              $faq
     * @param UpdateFAQRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Faq $faq, UpdateFAQRequest $request)
    {
        $faq = $this->faqService->update($faq, $request->validated());

        return $faq
            ? response()->success(new FAQResource($faq))
            : response()->error([__('faq.update.failed')], __('faq.update.failed'));
    }

    /**
     * @param Faq $faq
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Faq $faq)
    {
        $response = $this->faqRepository->deleteModel($faq);

        return $response
            ? response()->success()
            : response()->error([__('faq.delete.failed')], __('question.delete.failed'));
    }

    /**
     * @param DefaultListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(DefaultListRequest $request)
    {
        $categories = $this->faqCategoryRepository->list($request->validated());

        return response()->success(new FaqCategoryListCollection($categories));
    }


    /**
     * @param CreateFaqCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCategory(CreateFaqCategoryRequest $request)
    {
        $faqCategory = $this->faqService->createCategory($request->validated());

        return $faqCategory
            ? response()->success(new FaqCategoryResource($faqCategory))
            : response()->error([__('faq.category.create.failed')], __('faq.category.create.failed'));
    }

    /**
     * Update an user
     *
     * @param FaqCategory              $category
     * @param UpdateFaqCategoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategory(FaqCategory $category, UpdateFaqCategoryRequest $request)
    {
        $category = $this->faqService->updateCategory($category, $request->validated());

        return $category
            ? response()->success(new FaqCategoryResource($category))
            : response()->error([__('faq.update.failed')], __('faq.update.failed'));
    }

    /**
     * @param FaqCategory $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCategory(FaqCategory $category)
    {
        $response = $this->faqCategoryRepository->deleteModel($category);

        return $response
            ? response()->success()
            : response()->error([__('faq.category.delete.failed')], __('category.delete.failed'));
    }
}
