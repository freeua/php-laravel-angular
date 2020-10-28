<?php

namespace App\Portal\Notifications\Order;

use App\Helpers\PortalHelper;
use App\Portal\Models\Order;

use App\Portal\Models\Supplier;
use App\Portal\Models\User as PortalUser;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\System\Models\User;

class OrderUploadInvoiceForSysAdmin extends Notification
{
    /**@var Order */
    public $order;

    public static $toMailCallback;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail(User $notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->order);
        }
        $portal = $this->order->portal;

        $url = config('app.system_admin_url') . '/admin/order/' . $this->order->id.'';
        $mail = (new MailMessage)
            ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
            ->subject("Upload der Rechnung zum Dienstrad-Auftrag {$this->order->number}")
            ->markdown('mail.portal.order.order-invoice-upload-sys-admin', [
                'order'    => $this->order,
                'styles'   => $notifiable->getNotificationStyles(),
                'user'     => $notifiable,
                'url'      => $url,
            ]);


        return $mail;
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
