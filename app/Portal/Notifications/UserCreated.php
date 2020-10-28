<?php

namespace App\Portal\Notifications;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class UserCreated
 *
 * @package App\Portal\Notifications
 */
class UserCreated extends Notification
{
    /**
     * @var string
     */
    public $password;

    /** @var EmailService */
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
     *
     * @param  User $notifiable
     *
     * @return string
     */
    private function getUrl($notifiable)
    {
        $url  = $notifiable->getFrontendFullUrl();

        if ($notifiable->isCompanyAdmin()) {
            $url = str_replace_last('/admin', '', $url);
        }

        $url .= '/login';

        return $url;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->getUrl($notifiable);
        $from = '';
        if (PortalHelper::getPortal()) {
            $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
            $name = PortalHelper::name();
        } else {
            $from = 'system@' . env('APP_URL_BASE');
            $name = 'System';
        }

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
        //     ->from($from, $name)
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
        $url = $this->getUrl($notifiable);
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
