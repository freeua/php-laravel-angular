<?php

namespace App\Portal\Repositories\Employee;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Models\Companies\Company;
use App\Portal\Models\FaqCategory;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Class FAQCategirtRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Company find(int $id, array $relations = [])
 */
class FaqCategoryRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'portal'   => 'portal_id',
        'company' => 'company.name',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'faq_categories.name',
        'faq_categories.description'
    ];

    /**
     * FaqCategoryRepository constructor.
     *
     * @param FaqCategory $faqCategory
     */
    public function __construct(FaqCategory $faqCategory)
    {
        $this->model = $faqCategory;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'faq_categories.id',
            'faq_categories.name',
            'faq_categories.description'])
            ->where(function ($q) {
                $q->where('faq_categories.portal_id', null)
                    ->where('faq_categories.company_id', null);
            })
            ->orWhere(function ($q) {
                $q->where('faq_categories.portal_id', PortalHelper::id())
                    ->where('faq_categories.company_id', null);
            })
            ->orWhere('faq_categories.company_id', AuthHelper::user()->company_id);


        return $this->processList($query, $params, $relationships);
    }
}
