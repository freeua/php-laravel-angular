<?php

namespace App\Portal\Notifications\Offer;

use App\Helpers\PortalHelper;
use App\Models\Email;
use App\Portal\Gates\Portal;
use App\Portal\Models\Offer;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

class OfferCreated extends Notification
{
    /**
     * @var offer
     */
    public $offer;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'offer_created';

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
                'employee' => $notifiable,
                'offer'    => $this->offer,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $notifiable->getFrontendFullUrl()
            ]
        );

        // return (new MailMessage)
        //     ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
        //     ->subject("Sie haben ein Angebot für Ihr Dienstrad erhalten!")
        //     ->markdown('mail.portal.offer.offer-created', [
        //         'employee' => $notifiable,
        //         'offer'    => $this->offer,
        //         'styles'   => $notifiable->getNotificationStyles(),
        //         'url'      => $notifiable->getFrontendFullUrl(),
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
                'offer'    => $this->offer,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $notifiable->getFrontendFullUrl()
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
