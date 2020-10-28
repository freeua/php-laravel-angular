<?php

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class offerCreated
 *
 * @package App\Portal\Notifications\offer
 */
class OfferRejectedForSupplier extends Notification
{
    /** @var offer
     */
    public $offer;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_rejected_supplier';
    public static $toMailCallback;

    public function __construct(
        Offer $offer
    ) {
        $this->offer = $offer;
        $this->emailService = app(EmailService::class);
    }

    public function via()
    {
        return ['mail', 'datatabase'];
    }

    public function toMail($supplier)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $supplier, $this->offer);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'offer'    => $this->offer,
                'supplier' => $supplier,
                'styles'   => $supplier->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Ihr Angebot wurde abgelehnt!')
        //     ->markdown('mail.portal.offer.offer-rejected-for-supplier', [
        //         'offer' => $this->offer,
        //         'supplier' => $supplier,
        //         'styles' => $supplier->getNotificationStyles(),
        //     ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($supplier)
    {
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'offer'    => $this->offer,
                'supplier' => $supplier,
                'styles'   => $supplier->getNotificationStyles()
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
