<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (App\Exceptions\StokHabisException $e) {
            return response()->json([
                'type' => 'https://localhost:8000/api/v1/problems/stok-habis',
                'title' => 'Stok tidak cukup',
                'status' => 409,
                'detail' => $e->getMessage(),
            ], 409, ['Content-Type' => 'application/problem+json']);
        });
    })->create();
