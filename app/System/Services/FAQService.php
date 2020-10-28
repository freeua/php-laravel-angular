<?php

namespace App\System\Services;

use App\Portal\Models\Faq;
use App\Portal\Models\FaqCategory;
use App\System\Repositories\FaqCategoryRepository;
use App\System\Repositories\FAQRepository;

/**
 * Class CompanyService
 *
 * @package App\Portal\Services
 */
class FAQService
{
    /** @var FaqCategoryRepository */
    /** @var FAQRepository */
    private $faqCategoryRepository;

    public function __construct(
        FaqCategoryRepository $faqCategoryRepository,
        FAQRepository $faqRepository
    ) {
        $this->faqCategoryRepository = $faqCategoryRepository;
        $this->faqRepository = $faqRepository;
    }

    /**
     * @param array $data
     *
     * @return FaqCategory|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $faq = $this->faqRepository->create($data);

        return $faq
            ? $faq->fresh()
            : false;
    }

    /**
     * @param Faq $faq
     * @param array $data
     * @return Faq|false
     */
    public function update(Faq $faq, array $data)
    {
        $response = $this->faqRepository->update($faq->id, $data);

        return $response
            ? $faq->fresh()
            : false;
    }

    /**
     * @param array $data
     *
     * @return FaqCategory|false
     * @throws \Exception
     */
    public function createCategory(array $data)
    {
        $category = $this->faqCategoryRepository->create($data);

        return $category
            ? $category->fresh()
            : false;
    }

    /**
     * @param FaqCategory $category
     * @param array $data
     * @return FaqCategory|false
     */
    public function updateCategory(FaqCategory $category, array $data)
    {
        $response = $this->faqCategoryRepository->update($category->id, $data);

        return $response
            ? $category->fresh()
            : false;
    }
}
