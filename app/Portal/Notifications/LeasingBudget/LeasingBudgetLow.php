<?php

namespace App\Portal\Notifications\LeasingBudget;

use App\Helpers\PortalHelper;
use App\Models\Companies\Company;
use App\Models\Portal;
use Illuminate\Notifications\Notification;
use App\Services\Emails\EmailService;

/**
 * Class ContractCreated
 *
 * @package App\Portal\Notifications\Contract
 */
class LeasingBudgetLow extends Notification
{

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * @var Company
     */
    public $company;
    public $percent;
    /**
     * @var EmailService
     */
    private $emailService;
    /** @var string */
    private $emailKey = 'leasing_budget_low';

    /**
     * Create a notification instance for low leasing budget.
     *
     * @param Company $company company refering to the leasing budget.
     * @param float $percent the percentage that's used on the mail
     */
    public function __construct(
        Company $company,
        float $percent
    ) {
        $this->company = $company;
        $this->percent = $percent;
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

    private function getSubject($notifiable)
    {
        $leasingBudgetFilled = number_format(100-$this->percent, 1, ',', '');
        $companyName = $this->company->name;
        $portalName = $this->company->portal->domain;

        $subject = "Your Leasing Budget is at $leasingBudgetFilled%";
        if ($notifiable instanceof Company) {
            $subject = "Leasing Budget of $companyName is at $leasingBudgetFilled%";
        }


        if ($notifiable instanceof \App\System\Models\User || $notifiable instanceof Portal) {
            $subject = "Leasing Budget of $companyName of portal $portalName is at $leasingBudgetFilled%";
        }
        
        return $subject;
    }

    public function toMail($notifiable)
    {
        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();
        $subject = $this->getSubject($notifiable);

        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'company' => $this->company,
                'percent' => $this->percent,
                'styles'  => $notifiable->getNotificationStyles()
            ],
            null,
            $subject
        );

        // return (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject($subject)
        //     ->markdown('mail.portal.leasing-budget.leasing-budget-low', [
        //         'company' => $this->company,
        //         'percent' => $this->percent,
        //         'styles' => $notifiable->getNotificationStyles(),
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
        $subject = $this->getSubject($notifiable);
        $dbEmail = $this->emailService->get($this->emailKey);

        return [
            'subject'   => $subject,
            'from'      => PortalHelper::name(),
            'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
                'company' => $this->company,
                'percent' => $this->percent,
                'styles'  => $notifiable->getNotificationStyles()
            ])
        ];
    }
}
