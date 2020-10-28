<?php

namespace App\Portal\Notifications\Company;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Portal\Models\User as PortalUser;
use App\System\Models\User as SystemUser;
use App\Services\Emails\EmailService;
use Illuminate\Notifications\Notification;

class CompanyChangedNotification extends Notification
{
    /**
     * @var Company
     */
    public $company;

    /**
     * @var User
     */
    public $portalAdmin;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'company_changed';

    public $oldData;

    public $changedFields;

    public static $toMailCallback;

    public function __construct(
        Company $company,
        array $oldData,
        PortalUser $portalAdmin,
        array $changedFields
    ) {
        $this->oldData = $oldData;
        $this->company = $company;
        $this->portalAdmin = $portalAdmin;
        $this->changedFields = $changedFields;
        $this->emailService = app(EmailService::class);
    }

    public function via()
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'portalAdmin'   => $this->portalAdmin,
                'company'       => $this->company,
                'styles'        => $this->portalAdmin->getNotificationStyles(),
                'formatChanges' => new FormatChanges($this->changedFields, $this->oldData)
            ]
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Unternehmensinformationen geändert')
        //     ->markdown('mail.portal.company.changed', [
        //         'portalAdmin' => $this->portalAdmin,
        //         'company' => $this->company,
        //         'styles' => $this->portalAdmin->getNotificationStyles(),
        //         'formatChanges' => new FormatChanges($this->changedFields, $this->oldData),
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
                'portalAdmin'   => $this->portalAdmin,
                'company'       => $this->company,
                'styles'        => $this->portalAdmin->getNotificationStyles(),
                'formatChanges' => new FormatChanges($this->changedFields, $this->oldData)
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
