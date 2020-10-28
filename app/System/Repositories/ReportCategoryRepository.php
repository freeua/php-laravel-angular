<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\System\Models\ReportCategory;

/**
 * Class FeedbackCategoryRepository
 *
 * @package App\System\Repositories
 * @method ReportCategory find(int $id, array $relations = [])
 */
class ReportCategoryRepository extends BaseRepository
{
    /**
     * ReportCategoryRepository constructor.
     * @param ReportCategory $reportCategory
     */
    public function __construct(ReportCategory $reportCategory)
    {
        $this->model = $reportCategory;
    }
}
