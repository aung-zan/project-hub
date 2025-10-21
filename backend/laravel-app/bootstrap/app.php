<?php

use App\Http\Middleware\JWTAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => JWTAuthenticate::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * Exception that is thrown by Gate.
         */
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            \Log::info($e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.'
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $th, Request $request) {
            if ($request->expectsJson()) {
                \Log::info('Exception caught: ' . get_class($th));
                \Log::info('Exception message: ' . $th->getMessage());
                \Log::info('Exception code: ' . $th->getCode());

                $statusCode = (int) $th->getCode();

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong.'
                ], $statusCode != 0 ? $statusCode : 500);
            }
        });
    })->create();
