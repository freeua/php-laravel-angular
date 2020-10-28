<?php

namespace App\Portal\Notifications\Company;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Portal\Models\User as PortalUser;
use App\System\Models\User as SystemUser;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

class LeasingConditionChanged extends Notification
{
    /**
     * @var Company
     */
    public $company;

    /**
     * @var PortalUser
     */
    public $portalAdmin;
    /**
     * @var LeasingCondition
     */
    public $oldLeasingCondition;
    /**
     * @var LeasingCondition
     */
    public $newLeasingCondition;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'company_leasing_condition_changed';

    public static $toMailCallback;

    public function __construct(
        Company $company,
        PortalUser $portalAdmin,
        LeasingCondition $oldLeasingCondition,
        LeasingCondition $newLeasingCondition = null
    ) {
        $this->company = $company;
        $this->portalAdmin = $portalAdmin;
        $this->newLeasingCondition = $newLeasingCondition;
        $this->oldLeasingCondition = $oldLeasingCondition;
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
                'portalAdmin'         => $this->portalAdmin,
                'company'             => $this->company,
                'styles'              => $this->portalAdmin->getNotificationStyles(),
                'oldLeasingCondition' => $this->oldLeasingCondition,
                'newLeasingCondition' => $this->newLeasingCondition
            ]
        );
        // return (new MailMessage)
        //     ->from()
        //     ->subject('Unternehmensinformationen geändert')
        //     ->markdown('mail.portal.company.leasing-condition-changed', [
        //         'portalAdmin' => $this->portalAdmin,
        //         'company' => $this->company,
        //         'styles' => $this->portalAdmin->getNotificationStyles(),
        //         'oldLeasingCondition' => $this->oldLeasingCondition,
        //         'newLeasingCondition' => $this->newLeasingCondition,
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
                'portalAdmin'         => $this->portalAdmin,
                'company'             => $this->company,
                'styles'              => $this->portalAdmin->getNotificationStyles(),
                'oldLeasingCondition' => $this->oldLeasingCondition,
                'newLeasingCondition' => $this->newLeasingCondition
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
