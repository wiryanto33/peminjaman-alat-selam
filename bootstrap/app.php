<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust reverse proxies and forwarded headers (HTTPS behind proxy)
        $middleware->trustProxies(at: '*');

        // Exclude Livewire upload endpoint from CSRF since it uses signed URLs
        // and sometimes fails on certain hosting setups due to cookie policies.
        $middleware->validateCsrfTokens(except: ['livewire/*']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
