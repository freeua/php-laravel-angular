<?php

namespace App\System\Services;

use App\System\Notifications\CreateFeedback;
use App\System\Repositories\FeedbackRepository;

/**
 * Class FeedbackService
 *
 * @package App\System\Services
 */
class FeedbackService
{
    /** @var FeedbackRepository */
    private $feedbackRepository;

    /**
     * FeedbackService constructor.
     * @param FeedbackRepository $feedbackRepository
     */
    public function __construct(
        FeedbackRepository $feedbackRepository
    ) {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * @param array $data
     * @return \App\System\Models\Feedback|bool
     */
    public function create(array $data)
    {
        $feedback = $this->feedbackRepository->create($data);

        if ($feedback) {
            // Send email
            \Notification::route('mail', config('mail.feedback_email'))
                ->notify(new CreateFeedback($feedback->category->name, $feedback->body));
        }

        return $feedback;
    }
}
