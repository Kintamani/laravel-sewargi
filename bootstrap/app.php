<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // JWT
        $middleware->alias([
            'jwt' => JwtMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // JSON format for api URI
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Contoh: Menangani pengecualian HTTP umum lainnya secara lebih dinamis
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage() ?: 'HTTP error.',
            ], $e->getStatusCode());
        });

        // Contoh: Menangani semua pengecualian lainnya sebagai kesalahan server internal
        $exceptions->renderable(function (Throwable $e, Request $request) {
            $statusCode = 500;
            $message = 'Internal server error';
            if (config('app.debug')) {
                $message = $e->getMessage();
            }

            return response()->json([
                'message' => $message,
            ], $statusCode);
        });
    })->create();
