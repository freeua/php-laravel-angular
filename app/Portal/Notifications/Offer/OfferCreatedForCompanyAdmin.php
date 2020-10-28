<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08.07.2019
 * Time: 12:20
 */

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

class OfferCreatedForCompanyAdmin extends Notification
{
    /**
     * @var Offer
     */
    public $offer;

    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_created_company_admin';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

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

        if (!is_null(PortalHelper::id())) {
            $portal = PortalHelper::getPortal();
        } else {
            $portal = $this->offer->user->portal;
        }
        $from = $portal->subdomain.'@'.env('APP_URL_BASE');
        $name = $portal->name;
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'companyAdmin' => $notifiable,
                'offer'        => $this->offer,
                'styles'       => $notifiable->getNotificationStyles(),
                'url'          => $notifiable->getFrontendFullUrl("admin/offer/{$this->offer->id}")
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject("Ein Mitarbeiter*in hat ein Angebot für ein Dienstrad erhalten!")
        //     ->markdown('mail.portal.offer.offer-created-for-company-admin', [
        //         'companyAdmin' => $notifiable,
        //         'offer'    => $this->offer,
        //         'styles'   => $notifiable->getNotificationStyles(),
        //         'url'      => $notifiable->getFrontendFullUrl("admin/offer/{$this->offer->id}"),
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
        if (!is_null(PortalHelper::id())) {
            $portal = PortalHelper::getPortal();
        } else {
            $portal = $this->offer->user->portal;
        }
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'  => $dbEmail->subject,
            'from'     => $portal->name,
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'companyAdmin' => $notifiable,
                'offer'        => $this->offer,
                'styles'       => $notifiable->getNotificationStyles(),
                'url'          => $notifiable->getFrontendFullUrl("admin/offer/{$this->offer->id}")
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
