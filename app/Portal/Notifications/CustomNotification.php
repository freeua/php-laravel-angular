<?php

namespace App\Portal\Notifications;

use App\Services\Emails\EmailService;
use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

/**
 * Class CustomNotification
 *
 * @package App\Portal\Notifications
 */
class CustomNotification extends Notification
{
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'custom_notification';
    /** @var Array */
    private $data;

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
    public function __construct(
        Array $data
    ) {
        $this->emailService = app(EmailService::class);
        $this->data = $data;
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
        $senderName = '';
        if (PortalHelper::getPortal()) {
            $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
            $name = PortalHelper::name();
            $senderName = AuthHelper::user()->fullName;
        } else {
            $from = 'system@' . env('APP_URL_BASE');
            $name = 'System';
            $senderName = $name;
        }

        return [$from, $name, $senderName];
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
                'subject'   => $this->data['data']['subject'],
                'body'      => $this->data['data']['body'],
                'styles'    => $notifiable->getNotificationStyles(),
                'date'      => Carbon::now()->format('d.m.Y'),
                'time'      => Carbon::now()->format('H:i'),
                'user'      => $notifiable,
            ],
            null,
            $this->data['data']['subject']
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
        list($from, $name, $senderName) = $this->getFrom();
        $dbEmail = $this->emailService->get($this->emailKey);
        $user = AuthHelper::user();

        return [
            'subject'   => $this->data['data']['subject'],
            'from'      => $senderName,
            'sender_id' => $user->id,
            'sender_type' => get_class($user),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'subject'   => $this->data['data']['subject'],
                'body'      => $this->data['data']['body'],
                'styles'    => $notifiable->getNotificationStyles(),
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
