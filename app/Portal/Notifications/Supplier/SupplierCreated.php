<?php

namespace App\Portal\Notifications\Supplier;

use App\Helpers\PortalHelper;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class SupplierCreated
 *
 * @package App\Portal\Notifications\Supplier
 */
class SupplierCreated extends Notification
{
    /**
     * @var Supplier
     */
    public $supplier;
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'supplier_created';

    /**
     * Create a notification instance.
     *
     * @param  Supplier $supplier
     *
     * @return void
     */
    public function __construct()
    {
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
    public function toMail(User $notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->supplier);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'      => $notifiable,
                'styles'    => $notifiable->getNotificationStyles(),
                'url'       => $notifiable->getFrontendFullUrl('login')
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Ihre Lieferant wurde bereits im System angelegt!')
        //     ->markdown('mail.portal.supplier.supplier-created', [
        //         'user'      => $notifiable,
        //         'styles'    => $notifiable->getNotificationStyles(),
        //         'url'       => $notifiable->getFrontendFullUrl('login'),
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
                'user'      => $notifiable,
                'styles'    => $notifiable->getNotificationStyles(),
                'url'       => $notifiable->getFrontendFullUrl('login')
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
