<?php

namespace App\Modules\TechnicalServices\Notifications;

use App\Helpers\PortalHelper;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Services\Emails\EmailService;
use Illuminate\Notifications\Notification;

class CompletedTechnicalServiceForCompany extends Notification
{
    const EMAIL_KEY = 'technical-services/completed-technical-service-company';
    /**@var TechnicalService */
    public $technicalService;
    /**@var EmailService */
    public $emailService;

    public static $toMailCallback;

    public function __construct(TechnicalService $technicalService)
    {
        $this->technicalService = $technicalService;
        $this->emailService = app(EmailService::class);
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->technicalService);
        }

        $from = PortalHelper::subdomain() . '@' . env('APP_URL_BASE');
        $name = PortalHelper::name();

        return $this->emailService->create(
            self::EMAIL_KEY,
            $from,
            $name,
            [
                'styles' => $this->technicalService->user->getNotificationStyles(),
                'employeeName' => $this->technicalService->user->fullName,
                'company' => $this->technicalService->user->company->name,
                'supplierName' => $this->technicalService->supplierName,
                'serviceCode' => $this->technicalService->number,
                'frameNumber' => $this->technicalService->frameNumber,            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $dbEmail = $this->emailService->get(self::EMAIL_KEY);

        return [
            'subject' => $dbEmail->subject,
            'from' => PortalHelper::name(),
            'body' => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles' => $this->technicalService->user->getNotificationStyles(),
                'employeeName' => $this->technicalService->user->fullName,
                'company' => $this->technicalService->user->company->name,
                'supplierName' => $this->technicalService->supplierName,
                'serviceCode' => $this->technicalService->number,
                'frameNumber' => $this->technicalService->frameNumber,
                ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
