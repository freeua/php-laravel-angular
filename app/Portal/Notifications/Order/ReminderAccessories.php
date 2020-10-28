<?php

namespace App\Portal\Notifications\Order;

use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReminderAccessories extends Notification
{
    /**@var User */
    public $user;

    public static $toMailCallback;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->user);
        }
        $portal = $this->user->portal;

        return (new MailMessage)
            ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
            ->subject('Remind of accessories!')
            ->markdown('mail.portal.order.reminder-accessories', [
                'employee' => $this->user,
                'styles'   => $this->user->getNotificationStyles(),
            ]);
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
