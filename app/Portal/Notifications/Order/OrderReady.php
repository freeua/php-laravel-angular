<?php

namespace App\Portal\Notifications\Order;

use App\Helpers\PortalHelper;
use App\Portal\Models\Order;

use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class OrderReady
 *
 * @package App\Portal\Notifications\Order
 */
class OrderReady extends Notification
{
    /**@var Order */
    public $order;
    /**@var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'order_ready';
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param Order $order
     */
    public function __construct(
        Order $order
    ) {
        $this->order = $order;
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
            return call_user_func(static::$toMailCallback, $notifiable, $this->order);
        }
        $portal = $this->order->portal;

        $from = $portal->subdomain.'@'.env('APP_URL_BASE');
        $name = $portal->name;
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'employee' => $notifiable,
                'order'    => $this->order,
                'styles'   => $this->order->user->getNotificationStyles(),
            ]
        );

        // return (new MailMessage)
        //     ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
        //     ->subject('Ihr Dienstrad ist abholbereit!')
        //     ->markdown('mail.portal.order.order-ready', [
        //         'employee' => $notifiable,
        //         'order'    => $this->order,
        //         'styles'   => $this->order->user->getNotificationStyles(),
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
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'employee' => $notifiable,
                'order'    => $this->order,
                'styles'   => $this->order->user->getNotificationStyles(),
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
