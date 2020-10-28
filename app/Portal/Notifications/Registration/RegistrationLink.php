<?php

namespace App\Portal\Notifications\Registration;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class RegistrationLink
 *
 * @package App\Portal\Notifications\Registration
 */
class RegistrationLink extends Notification
{
    /**
     * RegistrationCompleted url.
     *
     * @var string
     */
    public $url;

    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'registration_link';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string $url
     *
     * @return void
     */
    public function __construct(
        $url
    ) {
        $this->url = $url;
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
            return call_user_func(static::$toMailCallback, $notifiable, $this->url);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'url'    => $this->url,
                'styles' => $notifiable->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Registrierungslink')
        //     ->markdown('mail.portal.registration.registration-link', [
        //         'url' => $this->url,
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
                'url'    => $this->url,
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
