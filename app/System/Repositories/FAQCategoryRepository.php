<?php

namespace App\System\Repositories;

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
     * @param array $data
     *
     * @return FaqCategory|false
     */
    public function create(array $data)
    {
        $model = $this->model->newInstance();

        $model->name = $data['name'];
        $model->description = $data['description'];
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
            'faq_categories.id',
            'faq_categories.name',
            'faq_categories.description',
            'faq_categories.portal_id'
        ]);

        return $this->processList($query, $params, $relationships);
    }
}
