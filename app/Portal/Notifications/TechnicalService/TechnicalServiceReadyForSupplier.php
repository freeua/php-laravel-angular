<?php

namespace App\Portal\Notifications\TechnicalService;

use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Models\Supplier;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicalServiceReadyForSupplier extends Notification
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

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->technicalService);
        }

        $portal = $this->technicalService->order->portal;
        $mail = (new MailMessage())
            ->from($portal->subdomain . '@' . env('APP_URL_BASE'), $portal->name)
            ->subject('Ready for supplier')
            ->markdown('mail.portal.technical-service.ready-for-supplier', [
                'styles'   => $this->technicalService->order->user->getNotificationStyles(),
                'user'     => $notifiable,
                'userName' => $notifiable instanceof Supplier ? $notifiable->admin_first_name : $notifiable->fullName,
            ]);

        return $mail;
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
