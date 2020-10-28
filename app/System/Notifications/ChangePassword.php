<?php

namespace App\System\Notifications;

use App\Helpers\PortalHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ChangePassword
 *
 * @package App\System\Notifications
 */
class ChangePassword extends Notification
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
    private $emailKey = 'change_password';

    /**
     * Create a notification instance.
     *
     *
     * @return void
     */
    public function __construct()
    {
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
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        $url = config('app.system_admin_url') . '/password/forgot-password';

        $styles = [
            'logo'      => 'system-logo.png',
            'color'     => '#EA4345'
        ];

        $from = "system@".env('APP_URL_BASE');
        $name = 'System';

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'styles'    => $styles,
                'domain'    => config('app.system_admin_domain'),
                'domainUrl' => config('app.system_admin_url'),
                'url'       => $url,
                'date'      => Carbon::now()->format('d.m.Y'),
                'time'      => Carbon::now()->format('H:i'),
                'user'      => $notifiable
            ]
        );

        // return (new MailMessage)
        //     ->from("system@".env('APP_URL_BASE'), 'System')
        //     ->subject('Sie haben Ihr Passwort geändert')
        //     ->markdown('mail.password-changed', [
        //         'styles'    => $styles,
        //         'domain'    => config('app.system_admin_domain'),
        //         'domainUrl' => config('app.system_admin_url'),
        //         'url'       => $url,
        //         'date'      => Carbon::now()->format('d.m.Y'),
        //         'time'      => Carbon::now()->format('H:i'),
        //         'user'      => $notifiable
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
        $url = config('app.system_admin_url') . '/password/forgot-password';
        $styles = [
            'logo'      => 'system-logo.png',
            'color'     => '#EA4345'
        ];
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles'    => $styles,
                'domain'    => config('app.system_admin_domain'),
                'domainUrl' => config('app.system_admin_url'),
                'url'       => $url,
                'date'      => Carbon::now()->format('d.m.Y'),
                'time'      => Carbon::now()->format('H:i'),
                'user'      => $notifiable
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
