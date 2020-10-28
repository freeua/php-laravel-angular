<?php
namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;

class MailgunWebhook
{
    public function handle($request, \Closure $next)
    {
        $body = $request->all();
        if ($this->verify(env('MAILGUN_SECRET'), $body['token'], $body['timestamp'], $body['signature'])) {
            return $next($request);
        }
        throw new ForbiddenException('Incorrect token and signature');
    }

    function verify($apiKey, $token, $timestamp, $signature)
    {
        // check if the timestamp is fresh
        if (abs(time() - $timestamp) > 15) {
            return false;
        }

        // returns true if signature is valid
        return hash_hmac('sha256', $timestamp . $token, $apiKey) === $signature;
    }
}
