<?php

namespace App\System\Services;

use App\System\Models\Report;
use App\System\Notifications\CreateReport;
use App\System\Repositories\ReportRepository;

/**
 * Class ReportService
 *
 * @package App\System\Services
 */
class ReportService
{
    /** @var ReportRepository */
    private $reportRepository;

    /**
     * FeedbackService constructor.
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @param array $data
     * @return Report|bool
     */
    public function create(array $data)
    {
        $report = $this->reportRepository->create($data);

        if ($report) {
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $id) {
                    $report->categories()->attach($id);
                }
            }

            // Send email
            \Notification::route('mail', config('mail.report_email'))
                ->notify(new CreateReport($report->categories()->pluck('name')->toArray(), $report->body));
        }

        return $report;
    }
}
