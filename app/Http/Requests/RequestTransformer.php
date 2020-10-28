<?php


namespace App\Http\Requests;

abstract class RequestTransformer
{
    private $request;

    public function __construct(ApiRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ApiRequest
    {
        return $this->request;
    }
}
