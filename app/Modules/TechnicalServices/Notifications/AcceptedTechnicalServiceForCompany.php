<?php

namespace App\Modules\TechnicalServices\Notifications;

use App\Helpers\PortalHelper;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Services\Emails\EmailService;
use Illuminate\Notifications\Notification;

class AcceptedTechnicalServiceForCompany extends Notification
{
    const EMAIL_KEY = 'technical-services/accepted-technical-service-company';
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
                'supplierName' => $this->technicalService->supplierName,
                'company' => $this->technicalService->user->company->name,
                'frameNumber' => $this->technicalService->frameNumber,
            ]
        );
    }

    public function toArray($notifiable)
    {
        $dbEmail = $this->emailService->get(self::EMAIL_KEY);

        return [
            'subject' => $dbEmail->subject,
            'from' => PortalHelper::name(),
            'body' => $this->emailService->getRenderedBody($dbEmail->body, [
                'styles' => $this->technicalService->user->getNotificationStyles(),
                'supplierName' => $this->technicalService->supplierName,
                'company' => $this->technicalService->user->company->name,
                'frameNumber' => $this->technicalService->frameNumber,
               ])
        ];
    }

    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
