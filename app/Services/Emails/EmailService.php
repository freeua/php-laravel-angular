<?php

namespace App\Services\Emails;

use App\Repositories\EmailRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Messages\MailMessage;

class EmailService
{
    /** @var EmailRepository */
    public $emailRepository;

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;
    }

    private function parseTemplate($content, $data)
    {
        $baseTempName = uniqid(rand(), true);
        $baseTempName = str_replace(".", "", $baseTempName);
        $tempName = $baseTempName . '.blade.php';
        $tempView = resource_path('views/vendor/mail/html/' . $tempName);
        file_put_contents($tempView, $content);

        $dbView = View::make('vendor.mail.html.'.$baseTempName, $data);
        return [$tempView, $dbView->render()];
    }

    public function get(string $key)
    {
        return $this->emailRepository->getByKey($key);
    }

    public function getRenderedBody(string $body, $data)
    {
        $tempView = '';
        try {
            list($tempView, $content) = $this->parseTemplate($body, $data);
            return $content;
        } catch (\Exception $exc) {
            throw $exc;
        } finally {
            if (File::exists($tempView)) {
                File::delete($tempView);
            }
        }
    }

    public function create(
        string $emailKey,
        string $from,
        string $name = '',
        Array $data,
        Array $attachments = null,
        string $customSubject = ''
    ) {
        $emailTemplate = $this->get($emailKey);

        $tempView = '';
        try {
            list($tempView, $content) = $this->parseTemplate($emailTemplate->body, $data);
            $data['slot'] = $content;

            $mailMessage = new MailMessage();
            $mailMessage
                ->from($from, $name)
                ->subject($emailTemplate->subject ?? $customSubject)
                ->markdown('vendor.mail.html.layout', $data);
            
            if ($attachments && count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    if ($attachment && count($attachment) > 2) {
                        $mailMessage->attachData($attachment[0], $attachment[1]);
                    } elseif ($attachment && count($attachment) > 1) {
                        $mailMessage->attach($attachment[0], $attachment[1]);
                    }
                }
            }

            return $mailMessage;
        } catch (\Exception $exc) {
            throw $exc;
        } finally {
            if (File::exists($tempView)) {
                File::delete($tempView);
            }
        }
    }
}
