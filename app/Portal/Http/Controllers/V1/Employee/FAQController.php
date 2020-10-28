<?php

namespace App\Portal\Http\Controllers\V1\Employee;

use App\Http\Requests\DefaultListRequest;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Resources\V1\ListCollections\FaqCategoryListCollection;
use App\Portal\Http\Resources\V1\ListCollections\FAQListCollection;
use App\Portal\Repositories\Employee\FaqCategoryRepository;
use App\Portal\Repositories\Employee\FAQRepository;
use Illuminate\Http\Request;

/**
 * Class FileController
 *
 * @package App\System\Http\Controllers
 */
class FAQController extends Controller
{

    /** @var FAQRepository */
    private $faqRepository;
    /** @var FaqCategoryRepository */
    private $faqCategoryRepository;

    public function __construct(
        FAQRepository $faqRepository,
        FaqCategoryRepository $faqCategoryRepository
    ) {
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
     * @param DefaultListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(DefaultListRequest $request)
    {
        $categories = $this->faqCategoryRepository->list($request->validated());

        return response()->success(new FaqCategoryListCollection($categories));
    }
}
