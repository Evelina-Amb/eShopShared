<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // All API responses are JSON
        if ($request->expectsJson()) {

            // 404 – Resource not found
            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'code' => 404
                ], 404);
            }

            // 401 – Unauthorized (not logged in)
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'code' => 401
                ], 401);
            }

            // 422 – Validation errors
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $exception->errors(),
                    'code' => 422
                ], 422);
            }

            // 403 – Forbidden (no permission)
            if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() === 403) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden',
                    'code' => 403
                ], 403);
            }

            // 500 – Internal server error (default)
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'code' => 500
            ], 500);
        }

        // Default Laravel HTML response (non-API)
        return parent::render($request, $exception);
    }
}
