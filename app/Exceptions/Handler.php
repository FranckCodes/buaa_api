<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Les données envoyées sont invalides.', 422, $e->errors());
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Non authentifié.', 401);
            }
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Accès non autorisé.', 403);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error('Ressource introuvable.', 404);
            }
        });

        $this->renderable(function (BusinessException $e, $request) {
            if ($request->expectsJson()) {
                return ApiResponse::error($e->getMessage(), $e->getStatus(), $e->getErrors());
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
                $message = config('app.debug') ? $e->getMessage() : 'Une erreur interne est survenue.';

                return ApiResponse::error($message, $status);
            }
        });
    }
}
