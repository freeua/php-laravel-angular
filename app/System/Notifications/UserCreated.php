<?php

namespace App\System\Notifications;

use App\Helpers\PortalHelper;
use App\Helpers\SystemHelper;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class UserCreated
 *
 * @package App\System\Notifications
 */
class UserCreated extends Notification
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'user_created';

    /**
     * Create a notification instance.
     *
     * @param  string $password
     */
    public function __construct(
        string $password
    ) {
        $this->password = $password;
        $this->emailService = app(EmailService::class);
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = SystemHelper::frontendUrl('login');
        $from = "system@".env('APP_URL_BASE');
        $name = 'System';
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'     => $notifiable,
                'password' => $this->password,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $url
            ]
        );

        // return (new MailMessage)
        //     ->from("system@".env('APP_URL_BASE'), 'System')
        //     ->subject('Benutzer erstellt')
        //     ->markdown('mail.portal.user-created', [
        //         'user'     => $notifiable,
        //         'password'  => $this->password,
        //         'styles'   => $notifiable->getNotificationStyles(),
        //         'url'      => $url
        //     ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $url = SystemHelper::frontendUrl('login');
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'user'     => $notifiable,
                'password' => $this->password,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $url
            ])
        ];
    }
}
