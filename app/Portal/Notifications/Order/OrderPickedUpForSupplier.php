<?php

namespace App\Portal\Notifications\Order;

use App\Helpers\PortalHelper;
use App\Portal\Models\Order;

use App\Portal\Models\Supplier;
use Illuminate\Notifications\Notification;
use App\Portal\Models\User;
use App\Services\Emails\EmailService;

class OrderPickedUpForSupplier extends Notification
{
    /**@var Order */
    public $order;

    public $offerCertificatePdf;

    public $leaseAgreementPdf;
    public $contractPdf;

    /**@var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'order_pickup_supplier';

    public static $toMailCallback;

    public function __construct(
        Order $order,
        $offerCertificatePdf,
        $leaseAgreementPdf,
        $contractPdf
    ) {
        $this->order = $order;
        $this->offerCertificatePdf = $offerCertificatePdf;
        $this->leaseAgreementPdf = $leaseAgreementPdf;
        $this->contractPdf = $contractPdf;
        $this->emailService = app(EmailService::class);
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
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
                'userName' => $notifiable instanceof Supplier ? $notifiable->admin_first_name : $notifiable->fullName
            ],
            [
                [storage_path("app/private/$this->offerCertificatePdf"), ['as' => 'Ubernahmebestatigung.pdf']]
            ]
        );

        // $mail = (new MailMessage())
        //     ->from($portal->subdomain . '@' . env('APP_URL_BASE'), $portal->name)
        //     ->subject('Ein Kunde hat sein Dienstrad übernommen')
        //     ->markdown('mail.portal.order.order-pickup-supplier', [
        //         'order'    => $this->order,
        //         'styles'   => $this->order->user->getNotificationStyles(),
        //         'userName'     => $notifiable instanceof Supplier ? $notifiable->admin_first_name : $notifiable->fullName,
        //     ])
        //     ->attach(storage_path("app/private/$this->offerCertificatePdf"), [
        //         'as' => 'Ubernahmebestatigung.pdf'
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
                'userName' => $notifiable instanceof Supplier ? $notifiable->admin_first_name : $notifiable->fullName
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
