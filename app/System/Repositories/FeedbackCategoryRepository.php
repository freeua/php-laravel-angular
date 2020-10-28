<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\System\Models\FeedbackCategory;

/**
 * Class FeedbackCategoryRepository
 *
 * @package App\System\Repositories
 * @method FeedbackCategory find(int $id, array $relations = [])
 */
class FeedbackCategoryRepository extends BaseRepository
{
    /**
     * FeedbackCategoryRepository constructor.
     * @param FeedbackCategory $feedbackCategory
     */
    public function __construct(FeedbackCategory $feedbackCategory)
    {
        $this->model = $feedbackCategory;
    }
}
