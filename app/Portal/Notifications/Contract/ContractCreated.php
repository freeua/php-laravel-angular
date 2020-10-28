<?php

namespace App\Portal\Notifications\Contract;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use App\Portal\Models\Contract;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ContractCreated
 *
 * @package App\Portal\Notifications\Contract
 */
class ContractCreated extends Notification
{
    /**
     * @var Contract
     */
    public $contract;
    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'contract_created';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  Contract $contract
     *.
     *
     * @return void
     */
    public function __construct(
        Contract $contract
    ) {
        $this->contract = $contract;
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
            return call_user_func(static::$toMailCallback, $notifiable, $this->contract);
        }
        $portal = $this->contract->portal;

        $from = $portal->subdomain.'@'.env('APP_URL_BASE');
        $name = $portal->name;
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'     => $notifiable,
                'contract' => $this->contract,
                'styles'   => $notifiable->getNotificationStyles()
            ]
        );

        // return (new MailMessage)
        //     ->from($portal->subdomain.'@'.env('APP_URL_BASE'), $portal->name)
        //     ->subject('Erfolgreiche Genehmigung eines Überlassungsvertrages!')
        //     ->markdown('mail.portal.contract.contract-created', [
        //         'user'     => $notifiable,
        //         'contract' => $this->contract,
        //         'styles'   => $notifiable->getNotificationStyles()
        //     ])
        // ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $portal = $this->contract->portal;
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'   => $dbEmail->subject,
            'from'      => $portal->name,
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'user'     => $notifiable,
                'contract' => $this->contract,
                'styles'   => $notifiable->getNotificationStyles()
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
