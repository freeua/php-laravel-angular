<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\System\Models\Feedback;

/**
 * Class FeedbackRepository
 *
 * @package App\System\Repositories
 * @method Feedback find(int $id, array $relations = [])
 */
class FeedbackRepository extends BaseRepository
{
    /**
     * FeedbackRepository constructor.
     * @param Feedback $feedback
     */
    public function __construct(Feedback $feedback)
    {
        $this->model = $feedback;
    }

    /**
     * @param array $data
     * @return Feedback|bool
     */
    public function create(array $data)
    {
        $feedback = $this->model->newInstance();
        $feedback->body = $data['body'];
        $feedback->category_id = $data['category_id'];
        $feedback->user_id = \Auth::id();

        return $feedback->save() ? $feedback : false;
    }
}
