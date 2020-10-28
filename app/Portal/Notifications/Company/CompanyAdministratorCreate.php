<?php

namespace App\Portal\Notifications\Company;

use App\Helpers\PortalHelper;
use App\Models\Portal;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class UserCreated
 *
 * @package App\Portal\Notifications
 */
class CompanyAdministratorCreate extends Notification
{
    /**
     * @var Portal
     */
    public $portal;

    /**
     * @var string
     */
    public $password;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'company_admin_created';

    /**
     * Create a notification instance.
     *
     * @param  Portal $portal
     * @param  string $password
     */
    public function __construct(
        Portal $portal,
        string $password
    ) {
        $this->portal = $portal;
        $this->password = $password;
        $this->emailService = app(EmailService::class);
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
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

        $url  = $notifiable->getFrontendFullUrl();

        if ($notifiable->isCompanyAdmin()) {
            $url = str_replace_last('/admin', '', $url);
        }

        $url .= '/login';

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'     => $notifiable,
                'password' => $this->password,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $url
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Ihre Zugangsdaten')
        //     ->markdown('mail.portal.company.admin-created', [
        //         'user'     => $notifiable,
        //         'password' => $this->password,
        //         'styles'   => $notifiable->getNotificationStyles(),
        //         'url'      => $url
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

        $url  = $notifiable->getFrontendFullUrl();

        if ($notifiable->isCompanyAdmin()) {
            $url = str_replace_last('/admin', '', $url);
        }
        
        $url .= '/login';

        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'   => $dbEmail->subject,
            'from'      => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'user'     => $notifiable,
                'password' => $this->password,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $url
            ])
        ];
    }
}
