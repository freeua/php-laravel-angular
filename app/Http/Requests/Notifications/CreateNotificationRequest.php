<?php

namespace App\Http\Requests\Notifications;

class CreateNotificationRequest extends \App\Http\Requests\ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'to.id' => 'required|integer',
            'subject' => 'required|string|max:180',
            'to.name' => 'required|string|max:180',
            'to.type' => 'required|string',
            'body' => 'required|string'
        ];
    }
}
