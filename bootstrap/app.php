<?php

use App\Http\Middleware\SetCacheHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // commands: __DIR__.'/../routes/console.php',
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('static', [
            SubstituteBindings::class,
            SetCacheHeaders::class,
        ]);

        // O webhook do Asaas vem de fora e nao tem sessao: a autenticacao
        // dele e o header asaas-access-token (ver AsaasWebhookController).
        $middleware->validateCsrfTokens(except: [
            'webhooks/asaas',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return response()->view('public.errors.404', [], 404);
            }
        });
    })
    ->withEvents(discover: false)
    ->create();
