<?php
namespace App\Webhooks\Controllers;

use App\Webhooks\Requests\MailgunRequest;
use App\Webhooks\Services\MailgunService;
use Illuminate\Routing\Controller;

class EmailController extends Controller
{
    public function handleAllMail(MailgunRequest $request)
    {
        return response()->json(MailgunService::handleReceivedEmail($request));
    }
}
