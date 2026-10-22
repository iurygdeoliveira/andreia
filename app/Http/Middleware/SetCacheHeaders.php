<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Livewire\Features\SupportDisablingBackButtonCache\SupportDisablingBackButtonCache;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (class_exists(SupportDisablingBackButtonCache::class)) {
            SupportDisablingBackButtonCache::$disableBackButtonCache = false;
        }

        // Cache por 1 hora no browser, 1 dia no CDN (Cloudflare)
        $response->headers->set('Cache-Control', 'max-age=3600, public, s-maxage=86400');

        return $response;
    }
}
