<?php

namespace App\Portal\Repositories\Company;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Models\Companies\Company;
use App\Portal\Models\Faq;
use App\Portal\Models\FaqCategory;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Class FAQCategirtRepository
 *
 * @package App\Portal\Repositories
 *
 * @method Company find(int $id, array $relations = [])
 */
class FAQRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'portal'   => 'portal_id',
        'company' => 'company.name',
    ];
    /** @var array */
    protected $searchWhereColumns = [
        'faqs.question',
        'faqs.answer'
    ];

    /**
     * FaqCategoryRepository constructor.
     *
     * @param FAQ $faq
     * @internal param FaqCategory $faqCategory
     */
    public function __construct(Faq $faq)
    {
        $this->model = $faq;
    }

    /**
     * @param array $data
     *
     * @return FaqCategory|false
     */
    public function create(array $data)
    {
        $model = $this->model->newInstance();

        $model->question = $data['question'];
        $model->answer = $data['answer'];
        $model->category_id = $data['category_id'];
        $model->author = AuthHelper::user()->fullName;
        $model->company_id = isset($data['company_id']) ? $data['company_id'] : null;
        $model->portal_id = isset($data['portal_id']) ? $data['portal_id'] : null;

        try {
            $model->save();
        } catch (QueryException $e) {
            return false;
        }
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->select([
            'faqs.id',
            'faqs.question',
            'faqs.answer',
            'faqs.author',
            'faqs.portal_id',
            'faqs.category_id',
            'faqs.company_id',
        ])
            ->where('faqs.visible', true)
            ->where(function ($q) {
                $q->where('faqs.portal_id', null)
                    ->where('faqs.company_id', null);
            })
            ->orWhere(function ($q) {
                $q->where('faqs.portal_id', PortalHelper::id())
                    ->where('faqs.company_id', null);
            })
            ->orWhere('faqs.company_id', AuthHelper::user()->company_id);


        return $this->processList($query, $params, $relationships);
    }
}
