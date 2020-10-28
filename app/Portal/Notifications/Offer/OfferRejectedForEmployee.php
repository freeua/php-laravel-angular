<?php

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class offerCreated
 *
 * @package App\Portal\Notifications\offer
 */
class OfferRejectedForEmployee extends Notification
{
    /** @var offer
     */
    public $offer;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_rejected_employee';

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

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->offer);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'offer'  => $this->offer,
                'user'   => $notifiable,
                'styles' => $notifiable->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Leider konnte Ihr Überlassungsvertrag nicht genehmigt werden!')
        //     ->markdown('mail.portal.offer.offer-rejected-for-employee', [
        //         'offer' => $this->offer,
        //         'user' => $notifiable,
        //         'styles' => $notifiable->getNotificationStyles(),
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
                'offer'  => $this->offer,
                'user'   => $notifiable,
                'styles' => $notifiable->getNotificationStyles()
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
