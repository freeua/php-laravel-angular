<?php


namespace App\Webhooks\Notifications;

use Illuminate\Mail\Mailable;

class Autoresponder extends Mailable
{
    public $theme = 'default';
    public $message = '';
    public function __construct($message)
    {
        $this->message = $message;
    }

    public function build()
    {
        return $this->markdown('mail.autoresponder', ['message' => $this->message]);
    }
}
