<?php

namespace App\Portal\Notifications\Registration;

use App\Models\Portal;
use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class RegistrationRejected
 *
 * @package App\Portal\Notifications\Registration
 */
class RegistrationApproved extends Notification
{
    public $portal;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'registration_approved';

    /**
     * Create a notification instance.
     *
     * @return void
     */
    public function __construct(
        Portal $portal
    ) {
        $this->portal = $portal;
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
     * @param  User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'      => $notifiable,
                'styles'    => $notifiable->getNotificationStyles(),
                'url'       => $notifiable->getFrontendFullUrl('login')
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Willkommen im Dienstrad-Portal!')
        //     ->markdown('mail.portal.registration.registration-approved', [
        //         'user'      => $notifiable,
        //         'styles'    => $notifiable->getNotificationStyles(),
        //         'url'       => $notifiable->getFrontendFullUrl('login'),
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
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'user'      => $notifiable,
                'styles'    => $notifiable->getNotificationStyles(),
                'url'       => $notifiable->getFrontendFullUrl('login')
            ])
        ];
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure $callback
     *
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
