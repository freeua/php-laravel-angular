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
class ContractsExported extends Notification
{
    /**
     * @var Contract
     */
    public $contract;
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /** @var EmailService */
    private $emailService;
    /** @var string */
    private $emailKey = 'contracts_exported';

    /**
     * Create a notification instance.
     *
     * @param $data
     * @param $filename
     * @param $path
     */
    public function __construct(
        $data,
        $filename,
        $path
    ) {
        $this->data = $data;
        $this->filename = $filename;
        $this->path = $path;
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
        $from = PortalHelper::subdomain().'@'.env('APP_URL_BASE');
        $name = PortalHelper::name();
        if ($this->data) {
            $attachment = [$this->data, $this->filename, 'data'];
        } else {
            $attachment = [$this->path, ['as' => $this->filename]];
        }
        
        return $this->emailService->create(
            $this->emailKey,
            $from,
            $name,
            [
                'styles'   => $notifiable->getNotificationStyles()
            ],
            [
                $attachment
            ]
        );
        
        // $mail = (new MailMessage)
        //     ->from(PortalHelper::subdomain().'@'.env('APP_URL_BASE'), PortalHelper::name())
        //     ->subject('Verträge exportiert')
        //     ->markdown('mail.portal.contract.contracts-exported', [
        //         'styles'   => $notifiable->getNotificationStyles(),
        //     ]);
        // if ($this->data) {
        //     $mail->attachData($this->data, $this->filename);
        // } else {
        //     $mail->attach($this->path, ['as' => $this->filename]);
        // }
        // return $mail;
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
