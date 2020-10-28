<?php

namespace App\System\Repositories;

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
        $model->author = 'Systemadministrator';
        $model->visible = $data['visible'];
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
            'faqs.visible',
            'faqs.portal_id',
            'faqs.category_id',
        ]);

        return $this->processList($query, $params, $relationships);
    }
}
