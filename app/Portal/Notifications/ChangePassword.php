<?php

namespace App\Portal\Notifications;

use App\Services\Emails\EmailService;
use App\Helpers\PortalHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

/**
 * Class ChangePassword
 *
 * @package App\Portal\Notifications
 */
class ChangePassword extends Notification
{
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'change_password';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

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
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        list($from, $name) = $this->getFrom();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'styles'    => $notifiable->getNotificationStyles(),
                'domain'    => PortalHelper::domain(),
                'domainUrl' => $notifiable->getFrontendFullUrl(),
                'url'       => $notifiable->getFrontendFullUrl('password/forgot-password'),
                'date'      => Carbon::now()->format('d.m.Y'),
                'time'      => Carbon::now()->format('H:i'),
                'user'      => $notifiable,
            ]
        );
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
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => $name,
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles'    => $notifiable->getNotificationStyles(),
                'domain'    => PortalHelper::domain(),
                'domainUrl' => $notifiable->getFrontendFullUrl(),
                'url'       => $notifiable->getFrontendFullUrl('password/forgot-password'),
                'date'      => Carbon::now()->format('d.m.Y'),
                'time'      => Carbon::now()->format('H:i'),
                'user'      => $notifiable,
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
