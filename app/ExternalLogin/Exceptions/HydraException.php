<?php
namespace App\ExternalLogin\Exceptions;

use App\Exceptions\ControlledException;
use GuzzleHttp\Exception\ClientException;

class HydraException extends ControlledException
{
    public $response;
    public function __construct(ClientException $exception)
    {
        $this->response = $exception->getResponse();
        parent::__construct(
            "Ein unerwarteter Fehler ist aufgetreten. Unser technisches Team wurde benachrichtigt. " . $this->response->getReasonPhrase(),
            $this->response->getStatusCode(),
            "hydraException",
            json_decode($this->response->getBody()->getContents(), true),
        );
    }
}
