<?php

namespace App\Portal\Notifications;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ResetPassword
 *
 * @package App\Portal\Notifications
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
     *
     * @param  User $notifiable
     *
     * @return string
     */
    private function getUrl($notifiable)
    {
        $url = $notifiable->getFrontendFullUrl();

        if ($notifiable->isCompanyAdmin()) {
            $url = str_replace_last('/admin', '', $url);
        }

        $url .= '/password/reset/?' . http_build_query(['token' => $this->token, 'email' => $notifiable->email]);

        return $url;
    }

    /**
     * Get the sender´s info.
     *
     * @return array|string
     */
    private function getFrom()
    {
        if (PortalHelper::getPortal()) {
            $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
            $name = PortalHelper::name();
        } else {
            $from = 'system@' . env('APP_URL_BASE');
            $name = 'System';
        }

        return [$from, $name];
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
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $url = $this->getUrl($notifiable);
        list($from, $name) = $this->getFrom();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'styles' => $notifiable->getNotificationStyles(),
                'url'    => $url,
                'date'   => Carbon::now()->format('F j, Y')
            ]
        );

        // return (new MailMessage)
        //     ->from($from, $name)
        //     ->subject('Passwort zurücksetzen')
        //     ->markdown('mail.password-reset', [
        //         'styles' => $notifiable->getNotificationStyles(),
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
        list($from, $name) = $this->getFrom();
        $url = $this->getUrl($notifiable);
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles' => $notifiable->getNotificationStyles(),
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
