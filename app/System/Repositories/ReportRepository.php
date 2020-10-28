<?php

namespace App\System\Repositories;

use App\Repositories\BaseRepository;
use App\System\Models\Report;

/**
 * Class ReportRepository
 *
 * @package App\System\Repositories
 * @method Report find(int $id, array $relations = [])
 */
class ReportRepository extends BaseRepository
{
    /**
     * FeedbackRepository constructor.
     * @param Report $report
     */
    public function __construct(Report $report)
    {
        $this->model = $report;
    }

    /**
     * @param array $data
     * @return Report|bool
     */
    public function create(array $data)
    {
        $report = $this->model->newInstance();
        $report->body = $data['body'];
        $report->user_id = \Auth::id();

        return $report->save() ? $report : false;
    }
}
