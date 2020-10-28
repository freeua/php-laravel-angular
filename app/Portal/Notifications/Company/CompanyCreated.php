<?php

namespace App\Portal\Notifications\Company;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Models\Portal;
use App\Portal\Models\User;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class CompanyCreated
 *
 * @package App\Portal\Notifications\Company
 */
class CompanyCreated extends Notification
{
    /**
     * @var Company
     */
    public $company;

    /**
     * @var Portal
     */
    public $portal;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'company_created';

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  Company $company
     * @param  Portal $portal
     *
     * @return void
     */
    public function __construct(
        Company $company,
        Portal $portal
    ) {
        $this->company = $company;
        $this->portal = $portal;
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
            return call_user_func(static::$toMailCallback, $notifiable, $this->company);
        }

        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();
        $pdfPath = \Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($this->portal->policyPdf);

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'user'     => $notifiable,
                'company'  => $this->company,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $notifiable->getFrontendFullUrl('login')
            ],
            [
                [$pdfPath, ['as' => 'Datenschutz.pdf']]
            ]
        );
        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Ihre Registrierung im Dienstrad Portal')
        //     ->markdown('mail.portal.company.company-created', [
        //         'user'     => $notifiable,
        //         'company'  => $this->company,
        //         'styles'   => $notifiable->getNotificationStyles(),
        //         'url'       => $notifiable->getFrontendFullUrl('login'),
        //     ])
        //     ->attach(\Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($this->portal->policyPdf), ['as' => 'Datenschutz.pdf']);
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
                'user'     => $notifiable,
                'company'  => $this->company,
                'styles'   => $notifiable->getNotificationStyles(),
                'url'      => $notifiable->getFrontendFullUrl('login')
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
