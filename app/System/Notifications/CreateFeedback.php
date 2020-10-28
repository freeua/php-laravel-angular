<?php

namespace App\System\Notifications;

use App\Helpers\PortalHelper;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class CreateFeedback
 *
 * @package App\System\Notifications
 */
class CreateFeedback extends Notification
{
    /** @var string */
    public $category;
    /** @var string */
    public $body;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'feedback';

    /**
     * CreateFeedback constructor.
     *
     * @param string $category
     * @param string $body
     */
    public function __construct(
        string $category,
        string $body
    ) {
        $this->category = $category;
        $this->body = $body;
        $this->emailService = app(EmailService::class);
    }

    /**
     * Get the notification's channels.
     *
     * @return array|string
     */
    public function via()
    {
        return ['mail', 'database'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        $from = "system@".env('APP_URL_BASE');
        $name = 'System';
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'body' => $this->body
            ],
            null,
            $this->category
        );

        // return (new MailMessage)
        //     ->from("system@".env('APP_URL_BASE'), 'System')
        //     ->subject($this->category)
        //     ->view(
        //         'mail.feedback',
        //         ['body' => $this->body]
        //     );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'   => $this->category,
            'from'      => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'body' => $this->body
            ])
        ];
    }
}
