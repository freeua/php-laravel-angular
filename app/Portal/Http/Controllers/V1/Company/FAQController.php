<?php

namespace App\Portal\Http\Controllers\V1\Company;

use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\CreateFaqCategoryRequest;
use App\Portal\Http\Requests\V1\CreateFAQRequest;
use App\Portal\Http\Requests\V1\Company\UpdateFaqCategoryRequest;
use App\Portal\Http\Requests\V1\Company\UpdateFAQRequest;
use App\Portal\Http\Resources\V1\FaqCategoryResource;
use App\Portal\Http\Resources\V1\FAQResource;
use App\Portal\Http\Resources\V1\ListCollections\FaqCategoryListCollection;
use App\Portal\Http\Resources\V1\ListCollections\FAQListCollection;
use App\Models\Companies\Company;
use App\Portal\Models\Faq;
use App\Portal\Models\FaqCategory;
use App\Portal\Repositories\Company\FaqCategoryRepository;
use App\Portal\Repositories\Company\FAQRepository;
use App\Portal\Services\Company\FAQService;
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
            : response()->error([__('faq.delete.failed')], __('faq.delete.failed'));
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
     * @param FaqCategory $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryOptions(FaqCategory $category)
    {
        if ($category->company_id) {
            $companies = [];
            $portal = null;
        } elseif ($category->portal_id) {
            $companies = Company::where('portal_id', $category->portal_id)->get();
            $portal = null;
        } else {
            $companies = Company::all();
            $portal = 'all';
        }

        return response()->success([
                "companies" => $companies,
                "portal"    => $portal
        ]);
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
