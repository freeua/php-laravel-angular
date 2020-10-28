<?php

namespace App\Exceptions;

use App\Helpers\ArrayHelper;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Portal\Helpers\AuthHelper;

/**
 * Class Handler
 *
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     *
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $exceptionCode = null;
        switch (true) {
            case $exception instanceof TokenExpiredException:
                $message = 'token_expired';
                $errors = ['message' => 'token_expired'];
                $code = JsonResponse::HTTP_UNAUTHORIZED;
                break;
            case $exception instanceof TokenInvalidException:
                $message = 'token_invalid';
                $errors = ['message' => 'token_invalid'];
                $code = JsonResponse::HTTP_UNAUTHORIZED;
                break;
            case $exception instanceof AuthenticationException:
                $message = $exception->getMessage();
                $errors = ['message' => $exception->getMessage()];
                \Log::warning($exception->getMessage(), [
                    'request' => [
                        'path'=> $request->path(),
                    ],
                ]);
                $code = JsonResponse::HTTP_UNAUTHORIZED;
                break;
            case $exception instanceof NotFoundHttpException:
            case $exception instanceof ModelNotFoundException:
                $message = $exception->getMessage();
                $errors = ['message' => $exception->getMessage()];
                \Log::notice($exception->getMessage(), [
                    'request' => [
                        'path'=> $request->path(),
                    ],
                ]);
                $code = JsonResponse::HTTP_NOT_FOUND;
                break;
            case $exception instanceof ValidationException:
                $message = __('validation.failed');
                $exceptionCode = 'validationFailed';
                $errors = $exception->errors();
                $errors = ArrayHelper::dotToMulti($errors);
                \Log::notice($exception, [
                    'request' => ['path' => $request ],
                    'errors' => $errors
                ]);
                $code = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $message = __('exception.method_not_allowed');
                $errors = ['message' => __('exception.method_not_allowed')];
                \Log::error($exception->getMessage(), [
                    'request' => [
                        'path' => $request->path(),
                        'method' => $request->method(),
                    ],
                ]);
                $code = JsonResponse::HTTP_METHOD_NOT_ALLOWED;
                break;
            case $exception instanceof BadRequestHttpException:
                $message = $exception->getMessage();
                $errors = ['message' => $exception->getMessage()];
                \Log::error($exception->getMessage(), [
                    'request' => [
                        'path' => $request->path(),
                        'url' => $request->fullUrl(),
                        'payload' => $request->all(),
                    ],
                ]);
                $code = JsonResponse::HTTP_METHOD_NOT_ALLOWED;
                break;
            case $exception instanceof ForbiddenException:
            case $exception instanceof UnauthorizedException:
            case $exception instanceof AuthorizationException:
                $message = $exception->getMessage();
                $errors = ['message' => $exception->getMessage()];
                \Log::error($exception->getMessage(), [
                    'request' => [
                        'path' => $request->path(),
                        'requester' => $request->requester,
                        'user' => AuthHelper::user(),
                    ],
                ]);
                $code = JsonResponse::HTTP_FORBIDDEN;
                break;
            case $exception instanceof WrongRouteException:
                $message = __('auth.wrong_route');
                $errors = ['redirect_to' => $exception->getMessage()];
                $code = JsonResponse::HTTP_FORBIDDEN;
                break;
            case $exception instanceof HttpException:
                $message = __($exception->getMessage());
                $errors = ['message' => $exception->getMessage()];
                \Log::notice($exception->getMessage(), [
                    'request' => [
                        'path' => $request->path(),
                    ],
                    'exception' => $exception
                ]);
                /** @var HttpException $code */
                $code = $exception->getStatusCode();
                break;
            case $exception instanceof ControlledException:
                \Log::notice($exception->getMessage(), [
                    'request' => [
                        'path' => $request->path(),
                        'requester' => $request->requester,
                        'user' => AuthHelper::user(),
                    ]
                ]);
                /** @var HttpException $code */
                return response()->json($exception->getPayload(), $exception->getCode());
                break;
            default:
                $message = __('exception.internal');
                if (config('app.debug') == false) {
                    $errors = ['message' => __('exception.internal')];
                } else {
                    $errors = ['exception' => $exception->getMessage(), 'stack' => $exception->getTrace()];
                }
                \Log::alert($exception, ['request' => $request, 'exception' => $exception]);
                $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->error($errors, $message, $code, $exceptionCode);
    }
}
