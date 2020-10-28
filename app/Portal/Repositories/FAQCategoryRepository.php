<?php

namespace App\Portal\Repositories;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
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
        $model->company_id = isset($data['company_id']) ? $data['company_id'] : null;
        $model->portal_id = PortalHelper::id();

        try {
            $model->save();
        } catch (QueryException $e) {
            return false;
        }
        return $model;
    }

    /**
     * Update model
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data)
    {
        return $this->find($id)->update($data);
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
            'faq_categories.portal_id',
            'faq_categories.company_id',
        ])
            ->where('faq_categories.portal_id', null)
            ->orWhere('faq_categories.portal_id', PortalHelper::id());
        ;


        return $this->processList($query, $params, $relationships);
    }
}
