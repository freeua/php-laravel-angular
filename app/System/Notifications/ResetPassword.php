<?php

namespace App\System\Notifications;

use App\Helpers\PortalHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ResetPassword
 *
 * @package App\System\Notifications
 */
class ResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;
    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'password_reset';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     *
     * @return void
     */
    public function __construct(
        $token
    ) {
        $this->token = $token;
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
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url = config('app.system_admin_url') . '/password/reset?' . http_build_query(['token' => $this->token, 'email' => $notifiable->email]);

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
                'styles' => $styles,
                'url'    => $url,
                'date'   => Carbon::now()->format('F j, Y')
            ]
        );

        // return (new MailMessage)
        //     ->from("system@".env('APP_URL_BASE'), 'System')
        //     ->subject('Passwort zurücksetzen')
        //     ->markdown('mail.password-reset', [
        //         'styles' => $styles,
        //         'url'    => $url,
        //         'date'   => Carbon::now()->format('F j, Y')
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
        $url = config('app.system_admin_url') . '/password/reset?' . http_build_query(['token' => $this->token, 'email' => $notifiable->email]);
        $styles = [
            'logo'      => 'system-logo.png',
            'color'     => '#EA4345'
        ];
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles' => $styles,
                'url'    => $url,
                'date'   => Carbon::now()->format('F j, Y')
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
