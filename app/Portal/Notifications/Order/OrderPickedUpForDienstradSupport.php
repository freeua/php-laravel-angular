<?php

namespace App\Portal\Notifications\Order;

use App\Helpers\PortalHelper;
use App\Portal\Models\Order;

use Illuminate\Notifications\Notification;
use App\Portal\Models\User;
use App\Services\Emails\EmailService;

class OrderPickedUpForDienstradSupport extends Notification
{
    /**@var Order */
    public $order;

    public $offerCertificatePdf;

    public $leaseAgreementPdf;

    public static $toMailCallback;

    /**@var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'order_pickup_company_dienstrad_support';

    public function __construct(
        Order $order,
        $offerCertificatePdf,
        $leaseAgreementPdf
    ) {
        $this->order = $order;
        $this->offerCertificatePdf = $offerCertificatePdf;
        $this->leaseAgreementPdf = $leaseAgreementPdf;
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

        $attachments = [];

        if ($this->offerCertificatePdf) {
            array_push($attachments, [
                storage_path("app/private/$this->offerCertificatePdf"), ['as' => 'Ubernahmebestatigung.pdf']
            ]);
        }

        if ($this->leaseAgreementPdf) {
            array_push($attachments, [
                storage_path("app/private/$this->leaseAgreementPdf"), ['as' => 'Einzelleasing-Antrag.pdf']
            ]);
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
            $attachments
        );

        // $mail = (new MailMessage())
        //     ->from($portal->subdomain . '@' . env('APP_URL_BASE'), $portal->name)
        //     ->subject('Ein Mitarbeiter hat sein Dienstrad übernommen')
        //     ->markdown('mail.portal.order.order-pickup-company-for-dienstrad-support', [
        //         'order'    => $this->order,
        //         'styles'   => $this->order->user->getNotificationStyles(),
        //         'user'     => $notifiable,
        //     ]);
        // if ($this->offerCertificatePdf) {
        //     $mail->attach(storage_path("app/private/$this->offerCertificatePdf"), [
        //         'as' => 'Ubernahmebestatigung.pdf'
        //     ]);
        // }

        // if ($this->leaseAgreementPdf) {
        //     $mail->attach(storage_path("app/private/$this->leaseAgreementPdf"), [
        //         'as' => 'Einzelleasing-Antrag.pdf'
        //     ]);
        // }

        // if ($this->contractPdf) {
        //     $mail->attach(storage_path("app/private/$this->contractPdf"), [
        //         'as' => 'Uberlassungsvertrag.pdf'
        //     ]);
        // }

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
