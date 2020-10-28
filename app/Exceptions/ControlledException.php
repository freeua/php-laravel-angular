<?php
namespace App\Exceptions;

abstract class ControlledException extends \Exception
{
    protected $exceptionCode;
    protected $payload;

    public function __construct(
        string $message,
        int $httpCode,
        string $exceptionCode,
        array $payload = []
    ) {
        $this->exceptionCode = $exceptionCode;
        $this->payload = $payload;
        parent::__construct($message, $httpCode, null);
    }

    public function getExceptionCode(): string
    {
        return $this->exceptionCode;
    }

    public function getPayload(): array
    {
        return array_merge($this->payload, [
            'message' => $this->message,
            'exceptionCode' => $this->getExceptionCode(),
        ]);
    }
}
