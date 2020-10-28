<?php

namespace App\Portal\Notifications\Registration;

use App\Helpers\PortalHelper;
use App\Models\Portal;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class RegistrationCompleted
 *
 * @package App\Portal\Notifications\Registration
 */
class RegistrationCompleted extends Notification
{
    /**
     * @var Portal
     */
    public $portal;

    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'registration_completed';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

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
        $pdfPath = \Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($this->portal->policyPdf);

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user' => $notifiable,
                'styles' => $notifiable->getNotificationStyles()
            ],
            [
                [$pdfPath, ['as' => 'Datenschutzerklärung.pdf']]
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Ihre Registrierung im Dienstrad-Portal!')
        //     ->markdown('mail.portal.registration.registration-completed', [
        //         'user' => $notifiable,
        //         'styles' => $notifiable->getNotificationStyles(),
        //     ])
        //     ->attach(\Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($this->portal->policyPdf), ['as' => 'Datenschutzerklärung.pdf']);
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
                'user' => $notifiable,
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
