<?php

declare(strict_types=1);

use App\Http\Controllers\AcceptInviteController;
use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\CourseLandingController;
use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WebsiteLandingController;
use App\Models\Course;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Route;

Route::middleware(['static'])->get('/', WebsiteLandingController::class)->name('home');

Route::get('/privacidade', fn (): Factory|\Illuminate\Contracts\View\View => view('public.pages.privacidade'))->name('privacidade');

/*
 * Ponte de conversao (ver docs/cro/auditoria-conversao-landing.md).
 *
 * Deliberadamente FORA do grupo 'static': o SetCacheHeaders envia
 * s-maxage=86400, e uma pagina com token CSRF cacheada no Cloudflare por um
 * dia devolveria 419 em todo submit.
 */
Route::bind('cursoPublicado', fn (string $chave): Course => CourseLandingController::resolvePublished($chave) ?? abort(404));

Route::get('/curso/{cursoPublicado}', [CourseLandingController::class, 'show'])->name('curso.show');

Route::post('/curso/{cursoPublicado}/inscricao', [CourseLandingController::class, 'storeLead'])
    ->middleware('throttle:10,1')
    ->name('curso.lead');

Route::get('/curso/{cursoPublicado}/obrigado', [CourseLandingController::class, 'obrigado'])
    ->name('curso.obrigado');

/*
 * Exibicao de pagamento PIX e polling de status (UC-04).
 */
Route::get('/pedido/{pedido}', [OrderPaymentController::class, 'show'])
    ->middleware('throttle:10,1')
    ->name('pedido.pagamento');

Route::get('/pedido/{pedido}/status', [OrderPaymentController::class, 'status'])
    ->middleware('throttle:120,1')
    ->name('pedido.status');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Rota de compatibilidade para middlewares que usam route('login')
Route::get('/__compat-login', fn () => redirect()->to('/login'))->name('login');

Route::get(
    config('filament-tenant-members.routes.prefix', 'invite').'/{token}',
    AcceptInviteController::class
)
    ->middleware(config('filament-tenant-members.routes.middleware', ['web', 'throttle:10,1']))
    ->name('invite.accept');

/*
 * Webhook do Asaas (UC-04).
 *
 * Sem CSRF (a chamada vem de fora) e sem throttle: a fila do Asaas e serial
 * e pausa apos 15 falhas consecutivas, entao um 429 nosso travaria as
 * confirmacoes seguintes. A autenticacao e o header asaas-access-token,
 * validado no controller.
 */
Route::post('/webhooks/asaas', AsaasWebhookController::class)
    ->name('webhooks.asaas');
