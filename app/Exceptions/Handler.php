<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if (!($exception == null)) {
            $result = [
                'success' => false,
                'message' => null,
                'data' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString(),
                ],
            ];
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            if ($exception instanceof MethodNotAllowedHttpException) {
                $result['message'] = 'Method Not Allowed';
                $code = Response::HTTP_METHOD_NOT_ALLOWED;
            } else if ($exception instanceof NotFoundHttpException) {
                $result['message'] = 'Not Found';
                $code = Response::HTTP_NOT_FOUND;
            } else {
                $result['message'] = $exception->getMessage();
            }
            return response()->json($result, $code, [], JSON_PRETTY_PRINT);
        }
        return parent::render($request, $exception);
    }
}
