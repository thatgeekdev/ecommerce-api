<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A lista dos inputs que nunca devem ser mostrados em exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra handlers de exceções customizados.
     */
    public function register(): void
    {
        // 🔹 Validação (422)
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // 🔹 Modelo não encontrado (404)
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Recurso não encontrado',
                ], 404);
            }
        });

        // 🔹 Rota não encontrada (404)
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Endpoint não encontrado',
                ], 404);
            }
        });

        // 🔹 Método HTTP inválido (405)
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Método HTTP não permitido',
                ], 405);
            }
        });

        // 🔹 Falha de autenticação (401)
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Não autenticado',
                ], 401);
            }
        });

        // 🔹 Exceções HTTP genéricas (403, 500, etc.)
        $this->renderable(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: class_basename($e),
                ], $e->getStatusCode());
            }
        });

        // 🔹 Fallback para erros inesperados (500)
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Erro interno no servidor',
                    'error'   => config('app.debug') ? $e->getMessage() : 'Tente novamente mais tarde',
                ], 500);
            }
        });
    }
}
