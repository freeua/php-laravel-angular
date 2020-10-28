<?php

namespace App\Portal\Notifications\Company;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Portal\Models\User as PortalUser;
use App\System\Models\User as SystemUser;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

class CompanyAdministratorChanged extends Notification
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
     * @var PortalUser
     */
    public $employee;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'change_password';

    public static $toMailCallback;

    public function __construct(
        Company $company,
        PortalUser $portalAdmin,
        PortalUser $employee
    ) {
        $this->company = $company;
        $this->portalAdmin = $portalAdmin;
        $this->employee = $employee;
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
                'portalAdmin' => $this->portalAdmin,
                'company' => $this->company,
                'styles' => $this->portalAdmin->getNotificationStyles(),
                'employee' => $this->employee,
                'permissions' => implode(',', $this->employee->permissions->pluck('label')->toArray()),
                'operation' => count($this->employee->permissions) > 0 ? 'add' : 'remove'
            ]
        );

        // return (new MailMessage)
        //     ->from()
        //     ->subject()
        //     ->markdown('mail.portal.company.admin-changed', [
        //         'portalAdmin' => $this->portalAdmin,
        //         'company' => $this->company,
        //         'styles' => $this->portalAdmin->getNotificationStyles(),
        //         'employee' => $this->employee,
        //         'permissions' => implode(',', $this->employee->permissions->pluck('label')->toArray()),
        //         'operation' => count($this->employee->permissions) > 0 ? 'add' : 'remove',
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
            'subject'   => $dbEmail->subject,
            'from'      => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'portalAdmin' => $this->portalAdmin,
                'company' => $this->company,
                'styles' => $this->portalAdmin->getNotificationStyles(),
                'employee' => $this->employee,
                'permissions' => implode(',', $this->employee->permissions->pluck('label')->toArray()),
                'operation' => count($this->employee->permissions) > 0 ? 'add' : 'remove'
            ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
