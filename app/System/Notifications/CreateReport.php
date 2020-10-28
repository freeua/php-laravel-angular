<?php

namespace App\System\Notifications;

use App\Helpers\PortalHelper;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class CreateReport
 *
 * @package App\System\Notifications
 */
class CreateReport extends Notification
{
    /** @var array */
    public $categories;
    /** @var string */
    public $body;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'report';

    /**
     * CreateReport constructor.
     * @param array $categories
     * @param string $body
     * @param EmailService $emailService
     */
    public function __construct(
        array $categories,
        string $body
    ) {
        $this->categories = $categories;
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
                'categories' => $this->categories,
                'body'       => $this->body
            ]
        );

        // return (new MailMessage)
        //     ->from("system@".env('APP_URL_BASE'), 'System')
        //     ->subject('Report')
        //     ->view(
        //         'mail.report',
        //         [
        //             'categories' => $this->categories,
        //             'body' => $this->body
        //         ]
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
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'categories' => $this->categories,
                'body'       => $this->body
            ])
        ];
    }
}
