<?php


namespace App\Webhooks\Requests;

use App\Http\Requests\ApiRequest;

class MailgunRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'sender' => 'required|string',
            'subject' => 'required|string',
            'body-html' => 'required|string',
            'attachment-count' => 'string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
