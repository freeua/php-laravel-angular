<?php

namespace App\Portal\Notifications\Registration;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class RegistrationNew
 *
 * @package App\Portal\Notifications\Registration
 */
class RegistrationNew extends Notification
{
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'registration_new';

    /**
     * Create a notification instance.
     *
     * @param User $user
     */
    public function __construct(
        User $user
    ) {
        $this->user = $user;
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
                'user'   => $this->user,
                'url'    => $notifiable->getFrontendFullUrl('admin/users'),
                'styles' => $notifiable->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Prüfung der Berechtigung für den Zugang zum Dienstrad-Portal!')
        //     ->markdown('mail.portal.registration.registration-new', [
        //         'user' => $this->user,
        //         'url' => $notifiable->getFrontendFullUrl('admin/users'),
        //         'styles' => $notifiable->getNotificationStyles(),
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
                'user'   => $this->user,
                'url'    => $notifiable->getFrontendFullUrl('admin/users'),
                'styles' => $notifiable->getNotificationStyles()
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
