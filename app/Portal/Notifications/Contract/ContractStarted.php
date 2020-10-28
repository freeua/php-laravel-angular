<?php

namespace App\Portal\Notifications\Contract;

use App\Helpers\PortalHelper;
use App\Portal\Models\User;
use App\Portal\Models\Role;
use App\Portal\Models\Contract;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ContractStarted
 *
 * @package App\Portal\Notifications\Contract
 */
class ContractStarted extends Notification
{
    /** @var Contract */
    public $contract;
    /** @var string */
    private $domain;
    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'contract_started';
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
     * @param string    $domain
     */
    public function __construct(
        Contract $contract,
        string $domain
    ) {
        $this->contract = $contract;
        $this->domain = $domain;
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

    private function getUrl($notifiable)
    {
        switch ($notifiable->getRoleNames()->first()) {
            case Role::ROLE_PORTAL_ADMIN:
                $url = '#';
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $url = $notifiable->getFrontendFullUrl('contract/' . $this->contract->id);
                break;
            case Role::ROLE_SUPPLIER_ADMIN:
                $url = $notifiable->getFrontendFullUrl('admin/contract/' . $this->contract->id);
                break;
            case Role::ROLE_EMPLOYEE:
            default:
                $url = $notifiable->getFrontendFullUrl('contract/' . $this->contract->id);
                break;
        }

        return $url;
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

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();
        $url = $this->getUrl($notifiable);
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'styles'     => $notifiable->getNotificationStyles(),
                'url'        => $url,
                'contract'   => $this->contract,
                'domain'     => $this->domain,
                'domainUrl'  => $notifiable->getFrontendFullUrl(),
                'notifiable' => $notifiable
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Neuer Vertrag')
        //     ->markdown('mail.portal.contract.contract-started', [
        //         'styles'     => $notifiable->getNotificationStyles(),
        //         'url'        => $url,
        //         'contract'   => $this->contract,
        //         'domain'     => $this->domain,
        //         'domainUrl'  => $notifiable->getFrontendFullUrl(),
        //         'notifiable' => $notifiable,
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
        $url = $this->getUrl($notifiable);
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'   => $dbEmail->subject,
            'from'      => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles'     => $notifiable->getNotificationStyles(),
                'url'        => $url,
                'contract'   => $this->contract,
                'domain'     => $this->domain,
                'domainUrl'  => $notifiable->getFrontendFullUrl(),
                'notifiable' => $notifiable
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
