<?php

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\Supplier;
use Illuminate\Notifications\Notification;
use App\Portal\Helpers\ContractPrices;
use App\Services\Emails\EmailService;

/**
 * Class offerCreated
 *
 * @package App\Portal\Notifications\offer
 */
class OfferApprovedForSupplier extends Notification
{
    /** @var offer
     */
    public $offer;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_approved_supplier';

    public static $toMailCallback;

    public function __construct(
        Offer $offer
    ) {
        $this->offer = $offer;
        $this->emailService = app(EmailService::class);
    }

    public function via()
    {
        return ['mail', 'database'];
    }

    public function toMail($supplier)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $supplier, $this->offer);
        }
        $portal = $this->offer->user->portal;
        $priceHelper = new ContractPrices($this->offer);
        // return (new MailMessage)
        //     ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
        //     ->subject('Bestellung')
        //     ->markdown('mail.portal.offer.offer-approved-for-supplier', [
        //         'offer' => $this->offer,
        //         'netPriceWithAccessories' => $priceHelper->getNetTotal()->formatTo('de'),
        //         'vatApplied' => $priceHelper->getVatApplied()->formatTo('de'),
        //         'grossPriceWithAccessories' => $priceHelper->getGrossTotal()->formatTo('de'),
        //         'portalName' => $portal->name,
        //         'supplier' => $supplier,
        //         'styles' => $portal->getNotificationStyles(),
        //     ]);
        $from = $portal->subdomain.'@'.env('APP_URL_BASE');
        $name = $portal->name;

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'offer'                     => $this->offer,
                'netPriceWithAccessories'   => $priceHelper->getNetTotal()->formatTo('de'),
                'vatApplied'                => $priceHelper->getVatApplied()->formatTo('de'),
                'grossPriceWithAccessories' => $priceHelper->getGrossTotal()->formatTo('de'),
                'portalName'                => $portal->name,
                'supplier'                  => $supplier,
                'styles'                    => $portal->getNotificationStyles()
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($supplier)
    {
        $portal = $this->offer->user->portal;
        $priceHelper = new ContractPrices($this->offer);
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'offer'                     => $this->offer,
                'netPriceWithAccessories'   => $priceHelper->getNetTotal()->formatTo('de'),
                'vatApplied'                => $priceHelper->getVatApplied()->formatTo('de'),
                'grossPriceWithAccessories' => $priceHelper->getGrossTotal()->formatTo('de'),
                'portalName'                => $portal->name,
                'supplier'                  => $supplier,
                'styles'                    => $portal->getNotificationStyles()
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
