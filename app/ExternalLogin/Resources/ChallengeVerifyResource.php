<?php
namespace App\ExternalLogin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeVerifyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'challenge' => $this->challenge,
            'skip' => $this->skip,
            'requestedScope' => $this->requested_scope,
            'client' => [
                'id' => $this->client->client_id,
                'name' => $this->client->client_name,
                'logo' => $this->client->logo_uri,
            ],
            'sessionId' => isset($this->session_id) ? $this->session_id : null,
            'redirectTo' => isset($this->redirect_to) ? $this->redirect_to : null,
        ];
    }
}
