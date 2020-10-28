<?php

namespace App\Portal\Notifications\TechnicalService;

use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicalServiceReadyService extends Notification
{
    /**@var TechnicalService */
    public $technicalService;

    public static $toMailCallback;

    public function __construct(TechnicalService $technicalService)
    {
        $this->technicalService = $technicalService;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail(User $notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->technicalService);
        }

        $portal = $this->technicalService->order->portal;
        $mail = (new MailMessage())
            ->from($portal->subdomain . '@' . env('APP_URL_BASE'), $portal->name)
            ->subject('Ready service')
            ->markdown('mail.portal.technical-service.ready-service', [
                'styles'   => $this->technicalService->order->user->getNotificationStyles(),
                'user'     => $notifiable,
            ]);

        return $mail;
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
