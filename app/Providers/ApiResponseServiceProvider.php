<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function (
            $payload = [],
            string $message = '',
            int $code = 200,
            array $headers = [],
            int $options = 0
        ) {
            /** @var \Illuminate\Support\Facades\Response $this */
            return Response::json(
                [
                    'success' => true,
                    'code' => $code,
                    'message' => $message,
                    'payload' => $payload
                ],
                $code,
                $headers,
                $options
            );
        });

        Response::macro('error', function (
            $payload,
            string $message = '',
            int $code = 500,
            string $exceptionCode = null
        ) {
            /** @var \Illuminate\Support\Facades\Response $this */
            return Response::json(
                [
                    'success' => false,
                    'exceptionCode' => !is_null($exceptionCode) ? $exceptionCode : null,
                    'message' => $message,
                    'payload' => $payload
                ],
                $code,
                [],
                0
            );
        });

        Response::macro('pagination', function (
            $payload,
            string $message = '',
            int $code = 200,
            array $headers = [],
            int $options = 0
        ) {
            return Response::json(
                [
                    'success' => true,
                    'code' => $code,
                    'message' => $message,
                    'payload' => [
                        'data' => $payload->getCollection(),
                        'meta' => [
                            'current_page' => $payload->currentPage(),
                            'from' => $payload->firstItem(),
                            'to' => $payload->lastItem(),
                            'per_page' => $payload->perPage(),
                            'total_pages' => $payload->lastPage(),
                            'total' => $payload->total(),
                        ],
                    ]
                ],
                $code,
                $headers,
                $options
            );
        });

        Response::macro('jsonPagination', function (
            $payload,
            string $message = '',
            int $code = 200,
            array $headers = [],
            int $options = 0
        ) {
            return Response::json(
                [
                    'data' => $payload->getCollection(),
                    'page' => $payload->currentPage(),
                    'from' => $payload->firstItem(),
                    'to' => $payload->lastItem(),
                    'size' => $payload->perPage(),
                    'totalPages' => $payload->lastPage(),
                    'totalElements' => $payload->total(),
                ],
                $code,
                $headers,
                $options
            );
        });
    }
}
