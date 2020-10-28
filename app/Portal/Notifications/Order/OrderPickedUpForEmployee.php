<?php

namespace App\Portal\Notifications\Order;

use App\Helpers\PortalHelper;
use App\Portal\Models\Order;

use Illuminate\Notifications\Notification;
use App\Portal\Models\User;
use App\Services\Emails\EmailService;

class OrderPickedUpForEmployee extends Notification
{
    /**@var Order */
    public $order;

    public $offerCertificatePdf;

    public $contractPdf;

    public static $toMailCallback;

    /**@var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'order_pickup_employee';

    public function __construct(
        Order $order,
        $offerCertificatePdf,
        $contractPdf
    ) {
        $this->order = $order;
        $this->offerCertificatePdf = $offerCertificatePdf;
        $this->contractPdf = $contractPdf;
        $this->emailService = app(EmailService::class);
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->order);
        }

        $portal = $this->order->portal;

        $from = $portal->subdomain . '@' . env('APP_URL_BASE');
        $name = $portal->name;
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'order'    => $this->order,
                'styles'   => $this->order->user->getNotificationStyles(),
                'user'     => $notifiable
            ],
            [
                [storage_path("app/private/$this->offerCertificatePdf"), ['as' => 'Ubernahmebestatigung.pdf']],
                [storage_path("app/private/$this->contractPdf"), ['as' => 'Uberlassungsvertrag.pdf']]
            ]
        );

        // $mail = (new MailMessage())
        //     ->from($portal->subdomain . '@' . env('APP_URL_BASE'), $portal->name)
        //     ->subject('Sie haben Ihr Dienstrad übernommen')
        //     ->markdown('mail.portal.order.order-pickup-employee', [
        //         'order'    => $this->order,
        //         'styles'   => $this->order->user->getNotificationStyles(),
        //         'user'     => $notifiable,
        //     ])
        //     ->attach(storage_path("app/private/$this->offerCertificatePdf"), [
        //         'as' => 'Ubernahmebestatigung.pdf'
        //     ])
        //     ->attach(storage_path("app/private/$this->contractPdf"), [
        //         'as' => 'Uberlassungsvertrag.pdf'
        //     ]);

        // return $mail;
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
                'order'    => $this->order,
                'styles'   => $this->order->user->getNotificationStyles(),
                'user'     => $notifiable
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
