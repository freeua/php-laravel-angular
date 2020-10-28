<?php

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Portal\Models\Contract;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class offerCreated
 *
 * @package App\Portal\Notifications\offer
 */
class OfferApprovedForPortalAdmin extends Notification
{
    /** @var offer
     */
    public $offer;
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_approved_portal_admin';

    /**
     * Create a notification instance.
     *
     * @param Offer $offer
     *
     */
    public function __construct(
        Offer $offer
    ) {
        $this->offer = $offer;
        $this->emailService = app(EmailService::class);
    }

    /**
     * Get the notification's channels.
     *
     * @return array|string
     */
    public function via()
    {
        return ['mail', 'database'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
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
                'portalAdmin' => $notifiable,
                'offer'       => $this->offer,
                'styles'      => $notifiable->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Angebot und Überlassungsvertrag wurden vom Arbeitnehmer genehmigt')
        //     ->markdown('mail.portal.offer.offer-approved-for-portal-admin', [
        //         'portalAdmin' => $notifiable,
        //         'offer'     => $this->offer,
        //         'styles'   => $notifiable->getNotificationStyles(),
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
                'portalAdmin' => $notifiable,
                'offer'       => $this->offer,
                'styles'      => $notifiable->getNotificationStyles()
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
