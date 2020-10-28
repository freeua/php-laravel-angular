<?php

namespace App\Portal\Services;

use App\Helpers\PortalHelper;
use App\Portal\Models\Faq;
use App\Portal\Models\FaqCategory;
use App\Portal\Repositories\FaqCategoryRepository;
use App\Portal\Repositories\FAQRepository;

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
     * @return FAQ|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $data['portal_id'] = PortalHelper::id();
        $response = $this->faqRepository->create($data);

        return $response
            ? $response->fresh()
            : false;
    }

    /**
     * @param Faq $faq
     * @param array $data
     * @return Faq|false
     */
    public function update(Faq $faq, array $data)
    {
        $data['portal_id'] = PortalHelper::id();

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
        $data['portal_id'] = PortalHelper::id();
        $response = $this->faqCategoryRepository->create($data);

        return $response
            ? $response->fresh()
            : false;
    }

    /**
     * @param FaqCategory $category
     * @param array $data
     * @return FaqCategory|false
     */
    public function updateCategory(FaqCategory $category, array $data)
    {
        $data['portal_id'] = PortalHelper::id();
        $response = $this->faqCategoryRepository->update($category->id, $data);

        return $response
            ? $category->fresh()
            : false;
    }
}
