<?php


namespace App\Webhooks\Services;

use App\Models\Portal;
use App\Webhooks\Exceptions\RecipientInvalid;
use App\Webhooks\Notifications\Autoresponder;
use App\Webhooks\Requests\MailgunRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MailgunService
{
    static function handleReceivedEmail(MailgunRequest $request)
    {
        $body = $request->all();
        return ['autoresponder' => self::sendAutoResponder($body['sender'], $body['recipient'])];
    }

    private static function sendAutoResponder($sender, $recipient)
    {
        return \Mail::to($sender)
            ->send(new Autoresponder(self::getAutoresponderHtml($recipient)));
    }

    private static function getAutoresponderHtml($recipient)
    {
        $recipientDelimited = explode('@', $recipient);
        if (is_null($recipient) || count($recipientDelimited) < 2) {
            throw new RecipientInvalid($recipient);
        }
        $subdomain = $recipientDelimited[0];
        try {
            $autoResponder = Portal::query()->where('subdomain', $subdomain)->firstOrFail()->autoresponderText;
            if (!is_null($autoResponder)) {
                return $autoResponder;
            }
            return null;
        } catch (ModelNotFoundException $error) {
            return null;
        }
    }
}
